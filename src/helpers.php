<?php

use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Facades\Schema;

if (! function_exists('schema')) {
    /**
     * Get the SchemaManager instance or load a schema.
     *
     * @param  string|null  $nsid
     * @return \SocialDept\Schema\SchemaManager|array
     */
    function schema(?string $nsid = null)
    {
        if ($nsid === null) {
            return app('schema');
        }

        return Schema::load($nsid);
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

if (! function_exists('schema_parse')) {
    /**
     * Parse a schema into a LexiconDocument.
     */
    function schema_parse(string $nsid): LexiconDocument
    {
        return Schema::parse($nsid);
    }
}

if (! function_exists('schema_generate')) {
    /**
     * Generate DTO code from a schema.
     */
    function schema_generate(string $nsid, array $options = []): string
    {
        return Schema::generate($nsid, $options);
    }
}


