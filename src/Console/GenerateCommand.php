<?php

namespace SocialDept\AtpSchema\Console;

use Illuminate\Console\Command;
use SocialDept\AtpSchema\Generator\DTOGenerator;
use SocialDept\AtpSchema\Parser\SchemaLoader;
use SocialDept\AtpSchema\Support\PathHelper;

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
                            {--dry-run : Preview generated code without writing files}
                            {--with-dependencies : Also generate all referenced lexicons recursively}
                            {--r|recursive : Alias for --with-dependencies}';

    /**
     * The console command description.
     */
    protected $description = 'Generate PHP DTO classes from ATProto Lexicon schemas';

    /**
     * Track generated NSIDs to avoid duplicates.
     */
    protected array $generated = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $nsid = $this->argument('nsid');
        $lexiconPath = config('schema.generators.lexicon_path', 'app/Lexicons');
        $output = $this->option('output') ?? base_path($lexiconPath);
        $namespace = $this->option('namespace') ?? PathHelper::pathToNamespace($lexiconPath);
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');
        $withDependencies = $this->option('with-dependencies') || $this->option('recursive');

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
                $document = $loader->load($nsid);
                $code = $generator->preview($document);

                $this->line('');
                $this->line('Generated code:');
                $this->line('─────────────────────────────────────────────────');
                $this->line($code);
                $this->line('─────────────────────────────────────────────────');

                return self::SUCCESS;
            }

            $allGenerated = [];
            $allSkipped = [];

            if ($withDependencies) {
                $this->info('Generating with dependencies...');
                $result = $this->generateWithDependencies($nsid, $loader, $generator, $force);
                $allGenerated = $result['generated'];
                $allSkipped = $result['skipped'];
            } else {
                $result = $generator->generateByNsidWithDetails($nsid, [
                    'dryRun' => false,
                    'overwrite' => $force,
                ]);
                $allGenerated = $result['generated'];
                $allSkipped = $result['skipped'];
            }

            $this->newLine();

            if (count($allGenerated) > 0) {
                $this->info('Generated '.count($allGenerated).' file(s):');
                foreach ($allGenerated as $file) {
                    $this->line("  <fg=green>✓</> {$file}");
                }
            }

            if (count($allSkipped) > 0) {
                $this->newLine();
                $this->warn('Skipped '.count($allSkipped).' file(s) (missing @generated marker):');
                foreach ($allSkipped as $file) {
                    $this->line("  <fg=yellow>⊘</> {$file}");
                }
            }

            $this->newLine();

            if (count($allGenerated) === 0 && count($allSkipped) === 0) {
                $this->info('No files to generate.');
            } else {
                $this->info('✓ Generation completed successfully');
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Generation failed: '.$e->getMessage());

            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }

    /**
     * Generate schema with all its dependencies recursively.
     *
     * @return array{generated: array<string>, skipped: array<string>}
     */
    protected function generateWithDependencies(
        string $nsid,
        SchemaLoader $loader,
        DTOGenerator $generator,
        bool $force
    ): array {
        // Skip if already generated
        if (in_array($nsid, $this->generated)) {
            return ['generated' => [], 'skipped' => []];
        }

        $this->line("  → Loading schema: {$nsid}");

        try {
            $schema = $loader->load($nsid);
        } catch (\Exception $e) {
            $this->warn("  ⚠ Could not load {$nsid}: ".$e->getMessage());

            return ['generated' => [], 'skipped' => []];
        }

        // Extract all referenced NSIDs from this schema
        $dependencies = $this->extractDependencies($schema);

        $allGenerated = [];
        $allSkipped = [];

        // Generate dependencies first
        foreach ($dependencies as $depNsid) {
            $depResult = $this->generateWithDependencies($depNsid, $loader, $generator, $force);
            $allGenerated = array_merge($allGenerated, $depResult['generated']);
            $allSkipped = array_merge($allSkipped, $depResult['skipped']);
        }

        // Mark as generated before generating to prevent circular references
        $this->generated[] = $nsid;

        // Generate current schema
        try {
            $result = $generator->generateByNsidWithDetails($nsid, [
                'dryRun' => false,
                'overwrite' => $force,
            ]);
            $allGenerated = array_merge($allGenerated, $result['generated']);
            $allSkipped = array_merge($allSkipped, $result['skipped']);
        } catch (\Exception $e) {
            $this->warn("  ⚠ Could not generate {$nsid}: ".$e->getMessage());
        }

        return ['generated' => $allGenerated, 'skipped' => $allSkipped];
    }

    /**
     * Extract all NSID dependencies from a schema.
     */
    protected function extractDependencies(\SocialDept\AtpSchema\Data\LexiconDocument $schema): array
    {
        $dependencies = [];
        $currentNsid = $schema->getNsid();

        // Walk through all definitions
        foreach ($schema->defs as $def) {
            $dependencies = array_merge($dependencies, $this->extractRefsFromDefinition($def));
        }

        // Filter out refs that are definitions within the same schema
        // (refs that start with the current NSID followed by a dot)
        $dependencies = array_filter($dependencies, function ($ref) use ($currentNsid) {
            return ! str_starts_with($ref, $currentNsid.'.');
        });

        return array_unique($dependencies);
    }

    /**
     * Recursively extract refs from a definition.
     */
    protected function extractRefsFromDefinition(array $definition): array
    {
        $refs = [];

        // Handle direct ref
        if (isset($definition['ref'])) {
            $ref = $definition['ref'];
            // Skip local references (starting with #)
            if (! str_starts_with($ref, '#')) {
                // Extract NSID part (before fragment)
                if (str_contains($ref, '#')) {
                    $ref = explode('#', $ref)[0];
                }
                $refs[] = $ref;
            }
        }

        // Handle union refs
        if (isset($definition['refs']) && is_array($definition['refs'])) {
            foreach ($definition['refs'] as $ref) {
                // Skip local references
                if (! str_starts_with($ref, '#')) {
                    // Extract NSID part
                    if (str_contains($ref, '#')) {
                        $ref = explode('#', $ref)[0];
                    }
                    $refs[] = $ref;
                }
            }
        }

        // Recursively check properties
        if (isset($definition['properties']) && is_array($definition['properties'])) {
            foreach ($definition['properties'] as $propDef) {
                $refs = array_merge($refs, $this->extractRefsFromDefinition($propDef));
            }
        }

        // Recursively check record
        if (isset($definition['record']) && is_array($definition['record'])) {
            $refs = array_merge($refs, $this->extractRefsFromDefinition($definition['record']));
        }

        // Recursively check array items
        if (isset($definition['items']) && is_array($definition['items'])) {
            $refs = array_merge($refs, $this->extractRefsFromDefinition($definition['items']));
        }

        return $refs;
    }
}
