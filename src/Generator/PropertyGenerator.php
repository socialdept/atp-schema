<?php

namespace SocialDept\AtpSchema\Generator;

class PropertyGenerator
{
    /**
     * Type mapper instance.
     */
    protected TypeMapper $typeMapper;

    /**
     * Stub renderer instance.
     */
    protected StubRenderer $renderer;

    /**
     * Create a new PropertyGenerator.
     */
    public function __construct(?TypeMapper $typeMapper = null, ?StubRenderer $renderer = null)
    {
        $this->typeMapper = $typeMapper ?? new TypeMapper();
        $this->renderer = $renderer ?? new StubRenderer();
    }

    /**
     * Generate a single property.
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string>  $required
     */
    public function generate(string $name, array $definition, array $required = []): string
    {
        $isRequired = in_array($name, $required);
        $phpType = $this->typeMapper->toPhpType($definition, ! $isRequired);
        $docType = $this->typeMapper->toPhpDocType($definition, ! $isRequired);
        $description = $definition['description'] ?? null;
        $default = $this->getDefaultValue($definition, $isRequired);

        return $this->renderer->render('property', [
            'docBlock' => $this->generateDocBlock($description, $docType),
            'visibility' => 'public ',
            'static' => '',
            'readonly' => 'readonly ',
            'type' => $phpType,
            'name' => $name,
            'default' => $default,
        ]);
    }

    /**
     * Generate multiple properties.
     *
     * @param  array<string, array<string, mixed>>  $properties
     * @param  array<string>  $required
     * @return array<string>
     */
    public function generateMultiple(array $properties, array $required = []): array
    {
        $result = [];

        foreach ($properties as $name => $definition) {
            $result[] = $this->generate($name, $definition, $required);
        }

        return $result;
    }

    /**
     * Generate property documentation block.
     */
    protected function generateDocBlock(?string $description, string $type): string
    {
        $lines = ['/**'];

        if ($description) {
            $lines[] = ' * '.$description;
            $lines[] = ' *';
        }

        $lines[] = ' * @var '.$type;
        $lines[] = ' */';

        return implode("\n", $lines);
    }

    /**
     * Get default value for property.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function getDefaultValue(array $definition, bool $isRequired): string
    {
        if ($isRequired) {
            return '';
        }

        $default = $this->typeMapper->getDefaultValue($definition);

        if ($default !== null) {
            return ' = '.$default;
        }

        return '';
    }

    /**
     * Generate property signature (for constructor parameters).
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string>  $required
     */
    public function generateSignature(string $name, array $definition, array $required = []): string
    {
        $isRequired = in_array($name, $required);
        $phpType = $this->typeMapper->toPhpType($definition, ! $isRequired);
        $default = $this->getDefaultValue($definition, $isRequired);

        return $phpType.' $'.$name.$default;
    }

    /**
     * Generate promoted property signature (for constructor).
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string>  $required
     */
    public function generatePromoted(string $name, array $definition, array $required = []): string
    {
        $isRequired = in_array($name, $required);
        $phpType = $this->typeMapper->toPhpType($definition, ! $isRequired);
        $default = $this->getDefaultValue($definition, $isRequired);

        return 'public readonly '.$phpType.' $'.$name.$default;
    }

    /**
     * Check if property should be nullable.
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string>  $required
     */
    public function isNullable(string $name, array $definition, array $required = []): bool
    {
        return ! in_array($name, $required);
    }

    /**
     * Get property type.
     *
     * @param  array<string, mixed>  $definition
     */
    public function getType(array $definition, bool $nullable = false): string
    {
        return $this->typeMapper->toPhpType($definition, $nullable);
    }

    /**
     * Get property doc type.
     *
     * @param  array<string, mixed>  $definition
     */
    public function getDocType(array $definition, bool $nullable = false): string
    {
        return $this->typeMapper->toPhpDocType($definition, $nullable);
    }
}
