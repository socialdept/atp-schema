<?php

namespace SocialDept\AtpSchema\Console;

use Illuminate\Console\Command;
use SocialDept\AtpSchema\Parser\SchemaLoader;

class ListCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'schema:list
                            {--filter= : Filter schemas by pattern (supports wildcards)}
                            {--type= : Filter by schema type (record, query, procedure, subscription)}';

    /**
     * The console command description.
     */
    protected $description = 'List all available ATProto Lexicon schemas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filter = $this->option('filter');
        $type = $this->option('type');

        try {
            $sources = config('schema.sources', []);
            $loader = new SchemaLoader($sources);

            $schemas = $this->discoverSchemas($sources);

            if ($filter) {
                $schemas = $this->filterSchemas($schemas, $filter);
            }

            if ($type) {
                $schemas = $this->filterByType($schemas, $type, $loader);
            }

            if (empty($schemas)) {
                $this->info('No schemas found');

                return self::SUCCESS;
            }

            $this->info('Found '.count($schemas).' schema(s):');
            $this->newLine();

            $tableData = [];

            foreach ($schemas as $nsid) {
                try {
                    $document = $loader->load($nsid);

                    $schemaType = 'unknown';
                    if ($document->isRecord()) {
                        $schemaType = 'record';
                    } elseif ($document->isQuery()) {
                        $schemaType = 'query';
                    } elseif ($document->isProcedure()) {
                        $schemaType = 'procedure';
                    } elseif ($document->isSubscription()) {
                        $schemaType = 'subscription';
                    }

                    $tableData[] = [
                        $nsid,
                        $schemaType,
                        $document->description ?? '-',
                    ];
                } catch (\Exception $e) {
                    $tableData[] = [
                        $nsid,
                        'error',
                        $e->getMessage(),
                    ];
                }
            }

            $this->table(['NSID', 'Type', 'Description'], $tableData);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to list schemas: '.$e->getMessage());

            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }

    /**
     * Discover all schema files in sources.
     *
     * @param  array<string>  $sources
     * @return array<string>
     */
    protected function discoverSchemas(array $sources): array
    {
        $schemas = [];

        foreach ($sources as $source) {
            if (! is_dir($source)) {
                continue;
            }

            $schemas = array_merge($schemas, $this->scanDirectory($source));
        }

        return array_unique($schemas);
    }

    /**
     * Scan directory for schema files.
     *
     * @return array<string>
     */
    protected function scanDirectory(string $directory, string $prefix = ''): array
    {
        $schemas = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory.'/'.$item;

            if (is_dir($path)) {
                $newPrefix = $prefix ? $prefix.'.'.$item : $item;
                $schemas = array_merge($schemas, $this->scanDirectory($path, $newPrefix));
            } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'json' || pathinfo($item, PATHINFO_EXTENSION) === 'php') {
                $name = pathinfo($item, PATHINFO_FILENAME);
                $nsid = $prefix ? $prefix.'.'.$name : $name;
                $schemas[] = $nsid;
            }
        }

        return $schemas;
    }

    /**
     * Filter schemas by pattern.
     *
     * @param  array<string>  $schemas
     * @return array<string>
     */
    protected function filterSchemas(array $schemas, string $pattern): array
    {
        $pattern = str_replace('*', '.*', preg_quote($pattern, '/'));

        return array_filter($schemas, function ($nsid) use ($pattern) {
            return preg_match("/^{$pattern}$/", $nsid);
        });
    }

    /**
     * Filter schemas by type.
     *
     * @param  array<string>  $schemas
     * @return array<string>
     */
    protected function filterByType(array $schemas, string $type, SchemaLoader $loader): array
    {
        return array_filter($schemas, function ($nsid) use ($type, $loader) {
            try {
                $document = $loader->load($nsid);

                return match ($type) {
                    'record' => $document->isRecord(),
                    'query' => $document->isQuery(),
                    'procedure' => $document->isProcedure(),
                    'subscription' => $document->isSubscription(),
                    default => false,
                };
            } catch (\Exception) {
                return false;
            }
        });
    }
}
