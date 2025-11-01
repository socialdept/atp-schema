<?php

namespace SocialDept\Schema\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'schema:clear-cache
                            {--nsid= : Clear cache for a specific NSID}
                            {--all : Clear all schema caches}';

    /**
     * The console command description.
     */
    protected $description = 'Clear ATProto Lexicon schema caches';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $nsid = $this->option('nsid');
        $all = $this->option('all');

        if (! $nsid && ! $all) {
            $this->error('Either --nsid or --all option must be provided');

            return self::FAILURE;
        }

        try {
            $cachePrefix = config('schema.cache.prefix', 'schema');

            if ($all) {
                $this->info('Clearing all schema caches...');

                // Clear all cache keys with the schema prefix
                if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                    Cache::tags([$cachePrefix])->flush();
                } else {
                    // For non-taggable stores, we need to clear by pattern
                    $this->warn('Cache store does not support tags. Using Cache::flush() to clear all caches.');
                    Cache::flush();
                }

                $this->info('✓ All schema caches cleared');

                return self::SUCCESS;
            }

            $this->info("Clearing cache for schema: {$nsid}");

            $cacheKey = "{$cachePrefix}:parsed:{$nsid}";
            Cache::forget($cacheKey);

            $this->info('✓ Cache cleared');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to clear cache: '.$e->getMessage());

            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }

            return self::FAILURE;
        }
    }
}
