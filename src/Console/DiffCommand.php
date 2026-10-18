<?php

namespace SocialDept\AtpSchema\Console;

use Illuminate\Console\Command;
use RuntimeException;
use SocialDept\AtpSchema\Authoring\LexiconDiff;
use SocialDept\AtpSchema\Authoring\LexiconFiles;
use SocialDept\AtpSchema\Authoring\NetworkLexicons;

class DiffCommand extends Command
{
    protected $signature = 'schema:diff
                            {nsid : The lexicon to compare}
                            {--against= : Compare with this file instead of the published version}';

    protected $description = 'Show how a local lexicon differs from its published version';

    public function handle(NetworkLexicons $network): int
    {
        $nsid = (string) $this->argument('nsid');
        $files = LexiconFiles::fromConfig();

        try {
            $local = $files->read($nsid);
            [$before, $label] = $this->baseline($network, $files, $nsid);
        } catch (RuntimeException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($before === null) {
            $this->components->info("{$nsid} is not published, so all of it is new.");

            return self::SUCCESS;
        }

        unset($before['$type'], $local['$type']);
        $changes = LexiconDiff::between($before, $local);

        if ($changes === []) {
            $this->components->info("{$nsid} matches {$label}.");

            return self::SUCCESS;
        }

        $this->components->info("{$nsid} against {$label}:");

        foreach ($changes as $change) {
            $line = match ($change['change']) {
                'added' => '<fg=green>+ '.$change['path'].'</> '.self::show($change['after']),
                'removed' => '<fg=red>- '.$change['path'].'</> '.self::show($change['before']),
                default => '<fg=yellow>~ '.$change['path'].'</> '.self::show($change['before']).' to '.self::show($change['after']),
            };

            $this->line('  '.$line);
        }

        return self::SUCCESS;
    }

    /**
     * @return array{0: array<string, mixed>|null, 1: string}
     */
    protected function baseline(NetworkLexicons $network, LexiconFiles $files, string $nsid): array
    {
        if (is_string($against = $this->option('against'))) {
            return [$files->decode($against), $against];
        }

        $published = $network->fetch($nsid);

        return [$published?->lexicon, $published === null ? 'nothing' : "the version at {$published->uri}"];
    }

    protected static function show(mixed $value): string
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
