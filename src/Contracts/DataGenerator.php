<?php

namespace SocialDept\Schema\Contracts;

use SocialDept\Schema\Data\LexiconDocument;

interface DataGenerator
{
    /**
     * Generate PHP class files from Lexicon definition.
     */
    public function generate(LexiconDocument $schema): string;

    /**
     * Generate and write class file to disk.
     */
    public function generateAndSave(LexiconDocument $schema, string $outputPath): string;

    /**
     * Generate class content without writing to disk.
     */
    public function preview(LexiconDocument $schema): string;

    /**
     * Set the base namespace for generated classes.
     */
    public function setBaseNamespace(string $namespace): void;

    /**
     * Set the output path for generated classes.
     */
    public function setOutputPath(string $path): void;
}
