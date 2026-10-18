<?php

namespace SocialDept\AtpSchema\Console\Concerns;

use SocialDept\AtpSchema\Authoring\Issue;

trait ReportsIssues
{
    /**
     * Print one lexicon's findings under its NSID.
     *
     * @param  array<int, Issue>  $issues
     */
    protected function reportIssues(string $nsid, array $issues): void
    {
        if ($issues === []) {
            $this->components->twoColumnDetail($nsid, '<fg=green>ok</>');

            return;
        }

        $levels = array_map(fn (Issue $issue) => $issue->level, $issues);

        $this->components->twoColumnDetail($nsid, match (true) {
            in_array(Issue::ERROR, $levels, true) => '<fg=red>errors</>',
            in_array(Issue::WARN, $levels, true) => '<fg=yellow>warnings</>',
            default => '<fg=gray>notes</>',
        });

        foreach ($issues as $issue) {
            $color = match ($issue->level) {
                Issue::ERROR => 'red',
                Issue::WARN => 'yellow',
                default => 'gray',
            };

            $this->line("    <fg={$color}>{$issue->level}</> [{$issue->name}] {$issue->message}");
        }
    }

    /**
     * @param  array<int, Issue>  $issues
     */
    protected function printIssuesJson(array $issues): void
    {
        foreach ($issues as $issue) {
            $this->line(json_encode($issue->toArray(), JSON_UNESCAPED_SLASHES));
        }
    }
}
