<?php

namespace SocialDept\AtpSchema\Console;

use Illuminate\Console\Command;
use RuntimeException;
use SocialDept\AtpSchema\Authoring\BreakingChanges;
use SocialDept\AtpSchema\Authoring\Issue;
use SocialDept\AtpSchema\Authoring\LexiconFiles;
use SocialDept\AtpSchema\Authoring\NetworkLexicons;
use SocialDept\AtpSchema\Console\Concerns\ReportsIssues;

class BreakingCommand extends Command
{
    use ReportsIssues;

    protected $signature = 'schema:breaking
                            {nsid?* : Lexicons to check, defaults to every local file}
                            {--against= : Compare one lexicon with this file instead of the published version}
                            {--json : One JSON finding per line}';

    protected $description = 'Check local lexicons for changes that would break their published versions';

    public function handle(NetworkLexicons $network): int
    {
        $files = LexiconFiles::fromConfig();
        $targets = $this->argument('nsid') ?: array_filter(array_keys($files->all()), fn (string $key) => ! str_ends_with($key, '.json'));
        $all = [];
        $failed = false;

        foreach ($targets as $nsid) {
            try {
                $local = $files->read($nsid);
                $before = is_string($against = $this->option('against')) ? $files->decode($against) : $network->fetch($nsid)?->lexicon;
            } catch (RuntimeException $e) {
                $this->components->twoColumnDetail($nsid, '<fg=red>'.$e->getMessage().'</>');
                $failed = true;

                continue;
            }

            if ($before === null) {
                $this->option('json') || $this->components->twoColumnDetail($nsid, '<fg=gray>not published</>');

                continue;
            }

            $issues = BreakingChanges::between($before, $local);
            array_push($all, ...$issues);
            $this->option('json') ? $this->printIssuesJson($issues) : $this->reportIssues($nsid, $issues);
        }

        return $failed || Issue::anyErrors($all) ? self::FAILURE : self::SUCCESS;
    }
}
