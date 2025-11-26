<?php

namespace SocialDept\AtpSchema\Generator;

use SocialDept\AtpSchema\Exceptions\GenerationException;

class StubRenderer
{
    /**
     * Path to stub files.
     */
    protected string $stubPath;

    /**
     * Cached stub contents.
     *
     * @var array<string, string>
     */
    protected array $stubs = [];

    /**
     * Create a new StubRenderer.
     */
    public function __construct(?string $stubPath = null)
    {
        $this->stubPath = $stubPath ?? $this->getDefaultStubPath();
    }

    /**
     * Render a stub with variables.
     *
     * @param  array<string, mixed>  $variables
     */
    public function render(string $stub, array $variables): string
    {
        $template = $this->loadStub($stub);

        return $this->replaceVariables($template, $variables);
    }

    /**
     * Load a stub file.
     */
    protected function loadStub(string $name): string
    {
        if (isset($this->stubs[$name])) {
            return $this->stubs[$name];
        }

        $path = $this->getStubPath($name);

        if (! file_exists($path)) {
            throw GenerationException::templateNotFound($name);
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw GenerationException::cannotReadFile($path);
        }

        $this->stubs[$name] = $content;

        return $content;
    }

    /**
     * Replace variables in template.
     *
     * @param  array<string, mixed>  $variables
     */
    protected function replaceVariables(string $template, array $variables): string
    {
        $result = $template;

        foreach ($variables as $key => $value) {
            // Convert value to string
            $stringValue = $this->valueToString($value);

            // Replace {{ key }} with value
            $result = str_replace('{{ '.$key.' }}', $stringValue, $result);
        }

        // Remove any remaining unreplaced variables
        $result = preg_replace('/\{\{\s*\w+\s*\}\}/', '', $result);

        return $result;
    }

    /**
     * Convert a value to string for replacement.
     */
    protected function valueToString(mixed $value): string
    {
        if (is_array($value)) {
            return implode("\n", array_filter($value));
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return '';
        }

        return (string) $value;
    }

    /**
     * Get the path for a stub file.
     */
    protected function getStubPath(string $name): string
    {
        // Check for published stubs first (in Laravel app)
        $publishedPath = base_path('stubs/schema/'.$name.'.stub');
        if (file_exists($publishedPath)) {
            return $publishedPath;
        }

        // Fall back to package stubs
        return $this->stubPath.'/'.$name.'.stub';
    }

    /**
     * Get default stub path.
     */
    protected function getDefaultStubPath(): string
    {
        return __DIR__.'/../../stubs';
    }

    /**
     * Clear cached stubs.
     */
    public function clearCache(): void
    {
        $this->stubs = [];
    }

    /**
     * Set custom stub path.
     */
    public function setStubPath(string $path): void
    {
        $this->stubPath = $path;
        $this->clearCache();
    }

    /**
     * Get available stubs.
     *
     * @return array<string>
     */
    public function getAvailableStubs(): array
    {
        $stubs = [];

        foreach (glob($this->stubPath.'/*.stub') as $file) {
            $stubs[] = basename($file, '.stub');
        }

        return $stubs;
    }
}
