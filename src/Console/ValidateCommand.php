<?php

namespace SocialDept\AtpSchema\Console;

use Illuminate\Console\Command;
use SocialDept\AtpSchema\Parser\SchemaLoader;
use SocialDept\AtpSchema\Validation\LexiconValidator;

class ValidateCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'schema:validate
                            {nsid : The NSID of the schema to validate}
                            {--data= : JSON data to validate against the schema}
                            {--file= : Path to file containing JSON data to validate}';

    /**
     * The console command description.
     */
    protected $description = 'Validate data against ATProto Lexicon schemas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $nsid = $this->argument('nsid');
        $dataJson = $this->option('data');
        $dataFile = $this->option('file');

        if (! $dataJson && ! $dataFile) {
            $this->error('Either --data or --file option must be provided');

            return self::FAILURE;
        }

        try {
            // Load data
            if ($dataFile) {
                if (! file_exists($dataFile)) {
                    $this->error("File not found: {$dataFile}");

                    return self::FAILURE;
                }

                $dataJson = file_get_contents($dataFile);
            }

            $data = json_decode($dataJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON: '.json_last_error_msg());

                return self::FAILURE;
            }

            // Load schema and validate
            $sources = config('schema.sources', []);
            $loader = new SchemaLoader($sources);
            $validator = new LexiconValidator($loader);

            $this->info("Validating data against schema: {$nsid}");

            $document = $loader->load($nsid);

            $errors = $validator->validateWithErrors($data, $document);

            if (empty($errors)) {
                $this->info('✓ Validation passed');

                return self::SUCCESS;
            }

            $this->error('✗ Validation failed:');
            $this->newLine();

            foreach ($errors as $field => $fieldErrors) {
                $this->line("  {$field}:");
                foreach ($fieldErrors as $error) {
                    $this->line("    - {$error}");
                }
            }

            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error('Validation error: '.$e->getMessage());

            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }
}
