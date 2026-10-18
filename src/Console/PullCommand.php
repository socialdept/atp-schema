<?php

namespace SocialDept\AtpSchema\Console;

use Illuminate\Console\Command;
use RuntimeException;
use SocialDept\AtpSchema\Authoring\LexiconDiff;
use SocialDept\AtpSchema\Authoring\LexiconFiles;
use SocialDept\AtpSchema\Authoring\LexiconLock;
use SocialDept\AtpSchema\Authoring\NetworkLexicons;
use SocialDept\AtpSupport\Nsid;

class PullCommand extends Command
{
    protected $signature = 'schema:pull
                            {nsid?* : Lexicons to pull, defaults to every one in the lock file}
                            {--force : Replace local copies that differ from the published version}';

    protected $description = 'Copy published lexicons from the network into the app, and record where they came from';

    public function handle(NetworkLexicons $network): int
    {
        $files = LexiconFiles::fromConfig();
        $lock = LexiconLock::fromConfig();
        $targets = $this->argument('nsid') ?: array_keys($lock->all());
        $failed = false;

        if ($targets === []) {
            $this->components->warn('Name the lexicons to pull, such as schema:pull com.example.thing.');

            return self::SUCCESS;
        }

        foreach ($targets as $nsid) {
            if (! Nsid::isValid($nsid)) {
                $this->components->twoColumnDetail($nsid, '<fg=red>not a valid NSID</>');
                $failed = true;

                continue;
            }

            try {
                $published = $network->fetch($nsid);
            } catch (RuntimeException $e) {
                $this->components->twoColumnDetail($nsid, '<fg=red>'.$e->getMessage().'</>');
                $failed = true;

                continue;
            }

            if ($published === null) {
                $this->components->twoColumnDetail($nsid, '<fg=red>not published (no '.NetworkLexicons::dnsName($nsid).' record or no schema record)</>');
                $failed = true;

                continue;
            }

            $local = $files->exists($nsid) ? $files->read($nsid) : null;

            if ($local !== null && LexiconDiff::same($local, $published->lexicon)) {
                $lock->put($published);
                $this->components->twoColumnDetail($nsid, '<fg=gray>up to date</>');

                continue;
            }

            if ($local !== null && ! $this->option('force')) {
                $this->components->twoColumnDetail($nsid, '<fg=yellow>local copy differs, pass --force to replace it</>');

                continue;
            }

            $files->write($nsid, $published->lexicon);
            $lock->put($published);
            $this->components->twoColumnDetail($nsid, '<fg=green>'.($local === null ? 'pulled' : 'updated').'</> from '.$published->did);
        }

        $lock->save();

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
