<?php

namespace SocialDept\Schema\Console;

use Illuminate\Console\Command;
use SocialDept\Schema\Generator\DTOGenerator;
use SocialDept\Schema\Parser\SchemaLoader;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'schema:generate
                            {nsid : The NSID of the schema to generate}
                            {--output= : Output directory for generated files}
                            {--namespace= : Base namespace for generated classes}
                            {--force : Overwrite existing files}
                            {--dry-run : Preview generated code without writing files}';

    /**
     * The console command description.
     */
    protected $description = 'Generate PHP DTO classes from ATProto Lexicon schemas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $nsid = $this->argument('nsid');
        $output = $this->option('output') ?? config('schema.lexicons.output_path');
        $namespace = $this->option('namespace') ?? config('schema.lexicons.base_namespace');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        $this->info("Generating DTO classes for schema: {$nsid}");

        try {
            $sources = config('schema.sources', []);
            $loader = new SchemaLoader(
                sources: $sources,
                useCache: config('schema.cache.enabled', true),
                cacheTtl: config('schema.cache.schema_ttl', 3600),
                cachePrefix: config('schema.cache.prefix', 'schema'),
                dnsResolutionEnabled: config('schema.dns_resolution.enabled', true),
                httpTimeout: config('schema.http.timeout', 10)
            );

            $generator = new DTOGenerator(
                schemaLoader: $loader,
                baseNamespace: $namespace,
                outputDirectory: $output
            );

            if ($dryRun) {
                $this->info('Dry run mode - no files will be written');
                $schema = $loader->load($nsid);
                $document = \SocialDept\Schema\Data\LexiconDocument::fromArray($schema);
                $code = $generator->preview($document);

                $this->line('');
                $this->line('Generated code:');
                $this->line('─────────────────────────────────────────────────');
                $this->line($code);
                $this->line('─────────────────────────────────────────────────');

                return self::SUCCESS;
            }

            $files = $generator->generateByNsid($nsid, [
                'dryRun' => false,
                'overwrite' => $force,
            ]);

            $this->info('Generated '.count($files).' file(s):');

            foreach ($files as $file) {
                $this->line("  - {$file}");
            }

            $this->newLine();
            $this->info('✓ Generation completed successfully');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Generation failed: '.$e->getMessage());

            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }
}
