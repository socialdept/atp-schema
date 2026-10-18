<?php

namespace SocialDept\AtpSchema\Console;

use Illuminate\Console\Command;
use RuntimeException;
use SocialDept\AtpSchema\Authoring\Issue;
use SocialDept\AtpSchema\Authoring\LexiconFiles;
use SocialDept\AtpSchema\Authoring\LexiconLinter;
use SocialDept\AtpSchema\Console\Concerns\ReportsIssues;

class LintCommand extends Command
{
    use ReportsIssues;

    protected $signature = 'schema:lint
                            {nsid?* : Lexicons to lint, defaults to every local file}
                            {--json : One JSON finding per line}';

    protected $description = 'Check local lexicon files for errors and style problems';

    public function handle(LexiconLinter $linter): int
    {
        $files = LexiconFiles::fromConfig();
        $targets = $this->argument('nsid') ?: array_keys($files->all());
        $all = [];

        if ($targets === []) {
            $this->components->warn("No lexicon files under {$files->root()}.");

            return self::SUCCESS;
        }

        foreach ($targets as $target) {
            try {
                $lexicon = str_ends_with($target, '.json') ? $files->decode($target) : $files->read($target);
                $issues = $linter->lint($lexicon);
            } catch (RuntimeException $e) {
                $issues = [Issue::error($target, 'unreadable', $e->getMessage())];
            }

            array_push($all, ...$issues);
            $this->option('json') ? $this->printIssuesJson($issues) : $this->reportIssues($target, $issues);
        }

        return Issue::anyErrors($all) ? self::FAILURE : self::SUCCESS;
    }
}
