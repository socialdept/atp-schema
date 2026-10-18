<?php

namespace SocialDept\AtpSchema\Console;

use Illuminate\Console\Command;
use InvalidArgumentException;
use SocialDept\AtpSchema\Authoring\LexiconFiles;
use SocialDept\AtpSchema\Authoring\Templates;
use SocialDept\AtpSupport\Nsid;

class NewCommand extends Command
{
    protected $signature = 'schema:new
                            {type : record, query, procedure or permission-set}
                            {nsid : The new lexicon\'s NSID, under a domain you control}
                            {--force : Overwrite an existing file}';

    protected $description = 'Create a lexicon file from a template';

    public function handle(): int
    {
        $nsid = (string) $this->argument('nsid');

        if (! Nsid::isValid($nsid)) {
            $this->components->error("{$nsid} is not a valid NSID.");

            return self::FAILURE;
        }

        try {
            $lexicon = Templates::make((string) $this->argument('type'), $nsid);
        } catch (InvalidArgumentException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $files = LexiconFiles::fromConfig();

        if ($files->exists($nsid) && ! $this->option('force')) {
            $this->components->error("{$files->path($nsid)} already exists. Pass --force to overwrite it.");

            return self::FAILURE;
        }

        $this->components->info('Created '.$files->write($nsid, $lexicon).'. Fill in the TODOs, then run schema:lint.');

        return self::SUCCESS;
    }
}
