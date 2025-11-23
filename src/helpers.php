<?php

use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Facades\Schema;

if (! function_exists('schema')) {
    /**
     * Get the SchemaManager instance or load a schema.
     *
     * @return \SocialDept\Schema\SchemaManager|LexiconDocument
     */
    function schema(?string $nsid = null)
    {
        if ($nsid === null) {
            return app('schema');
        }

        return Schema::load($nsid);
    }
}

if (! function_exists('schema_find')) {
    /**
     * Find a schema by NSID (nullable version).
     */
    function schema_find(string $nsid): ?LexiconDocument
    {
        return Schema::find($nsid);
    }
}

if (! function_exists('schema_exists')) {
    /**
     * Check if a schema exists.
     */
    function schema_exists(string $nsid): bool
    {
        return Schema::exists($nsid);
    }
}

if (! function_exists('schema_validate')) {
    /**
     * Validate data against a schema.
     */
    function schema_validate(string $nsid, array $data): bool
    {
        return Schema::validate($nsid, $data);
    }
}

if (! function_exists('schema_generate')) {
    /**
     * Generate DTO code from a schema.
     */
    function schema_generate(string $nsid, ?string $outputPath = null): string
    {
        return Schema::generate($nsid, $outputPath);
    }
}
