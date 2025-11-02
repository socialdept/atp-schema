<?php

namespace SocialDept\Schema\Generator;

use SocialDept\Schema\Data\LexiconDocument;

class DocBlockGenerator
{
    /**
     * Type mapper instance.
     */
    protected TypeMapper $typeMapper;

    /**
     * Create a new DocBlockGenerator.
     */
    public function __construct(?TypeMapper $typeMapper = null)
    {
        $this->typeMapper = $typeMapper ?? new TypeMapper;
    }

    /**
     * Generate a class-level docblock with rich annotations.
     *
     * @param  array<string, mixed>  $definition
     */
    public function generateClassDocBlock(
        LexiconDocument $document,
        array $definition
    ): string {
        $lines = ['/**'];

        // Add description
        if ($document->description) {
            $lines = array_merge($lines, $this->wrapDescription($document->description));
            $lines[] = ' *';
        }

        // Add lexicon metadata
        $lines[] = ' * Lexicon: '.$document->getNsid();

        if (isset($definition['type'])) {
            $lines[] = ' * Type: '.$definition['type'];
        }

        // Add @property tags for magic access
        $properties = $definition['properties'] ?? [];
        $required = $definition['required'] ?? [];

        if (! empty($properties)) {
            $lines[] = ' *';
            foreach ($properties as $name => $propDef) {
                $isRequired = in_array($name, $required);
                $docType = $this->typeMapper->toPhpDocType($propDef, ! $isRequired);
                $desc = $propDef['description'] ?? '';

                if ($desc) {
                    $lines[] = ' * @property '.$docType.' $'.$name.' '.$desc;
                } else {
                    $lines[] = ' * @property '.$docType.' $'.$name;
                }
            }
        }

        // Add validation constraints as annotations
        if (! empty($properties)) {
            $constraints = $this->extractConstraints($properties, $required);
            if (! empty($constraints)) {
                $lines[] = ' *';
                $lines[] = ' * Constraints:';
                foreach ($constraints as $constraint) {
                    $lines[] = ' * - '.$constraint;
                }
            }
        }

        $lines[] = ' */';

        return implode("\n", $lines);
    }

    /**
     * Generate a property-level docblock.
     *
     * @param  array<string, mixed>  $definition
     */
    public function generatePropertyDocBlock(
        string $name,
        array $definition,
        bool $isRequired
    ): string {
        $lines = ['    /**'];

        // Add description
        if (isset($definition['description'])) {
            $lines = array_merge($lines, $this->wrapDescription($definition['description'], '     * '));
            $lines[] = '     *';
        }

        // Add type annotation
        $docType = $this->typeMapper->toPhpDocType($definition, ! $isRequired);
        $lines[] = '     * @var '.$docType;

        // Add validation constraints
        $constraints = $this->extractPropertyConstraints($definition);
        if (! empty($constraints)) {
            $lines[] = '     *';
            foreach ($constraints as $constraint) {
                $lines[] = '     * '.$constraint;
            }
        }

        $lines[] = '     */';

        return implode("\n", $lines);
    }

    /**
     * Generate a method-level docblock.
     *
     * @param  array<array{name: string, type: string, description?: string}>  $params
     */
    public function generateMethodDocBlock(
        ?string $description,
        ?string $returnType,
        array $params = [],
        ?string $throws = null
    ): string {
        $lines = ['    /**'];

        // Add description
        if ($description) {
            $lines = array_merge($lines, $this->wrapDescription($description, '     * '));
        }

        // Add blank line if we have params or return
        if ((! empty($params) || $returnType) && $description) {
            $lines[] = '     *';
        }

        // Add parameters
        foreach ($params as $param) {
            $desc = $param['description'] ?? '';
            if ($desc) {
                $lines[] = '     * @param  '.$param['type'].'  $'.$param['name'].'  '.$desc;
            } else {
                $lines[] = '     * @param  '.$param['type'].'  $'.$param['name'];
            }
        }

        // Add return type
        if ($returnType && $returnType !== 'void') {
            $lines[] = '     * @return '.$returnType;
        }

        // Add throws
        if ($throws) {
            $lines[] = '     * @throws '.$throws;
        }

        $lines[] = '     */';

        return implode("\n", $lines);
    }

    /**
     * Wrap a long description into multiple lines.
     *
     * @return array<string>
     */
    protected function wrapDescription(string $description, string $prefix = ' * '): array
    {
        $maxWidth = 80 - strlen($prefix);
        $words = explode(' ', $description);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            if (empty($currentLine)) {
                $currentLine = $word;
            } elseif (strlen($currentLine.' '.$word) <= $maxWidth) {
                $currentLine .= ' '.$word;
            } else {
                $lines[] = $prefix.$currentLine;
                $currentLine = $word;
            }
        }

        if (! empty($currentLine)) {
            $lines[] = $prefix.$currentLine;
        }

        return $lines;
    }

    /**
     * Extract validation constraints from properties.
     *
     * @param  array<string, array<string, mixed>>  $properties
     * @param  array<string>  $required
     * @return array<string>
     */
    protected function extractConstraints(array $properties, array $required): array
    {
        $constraints = [];

        // Required fields
        if (! empty($required)) {
            $constraints[] = 'Required: '.implode(', ', $required);
        }

        // Property-specific constraints
        foreach ($properties as $name => $definition) {
            $propConstraints = $this->extractPropertyConstraints($definition);
            foreach ($propConstraints as $constraint) {
                $constraints[] = $name.': '.trim(str_replace('@constraint', '', $constraint));
            }
        }

        return $constraints;
    }

    /**
     * Extract validation constraints for a single property.
     *
     * @param  array<string, mixed>  $definition
     * @return array<string>
     */
    protected function extractPropertyConstraints(array $definition): array
    {
        $constraints = [];

        // String constraints
        if (isset($definition['maxLength'])) {
            $constraints[] = '@constraint Max length: '.$definition['maxLength'];
        }

        if (isset($definition['minLength'])) {
            $constraints[] = '@constraint Min length: '.$definition['minLength'];
        }

        if (isset($definition['maxGraphemes'])) {
            $constraints[] = '@constraint Max graphemes: '.$definition['maxGraphemes'];
        }

        if (isset($definition['minGraphemes'])) {
            $constraints[] = '@constraint Min graphemes: '.$definition['minGraphemes'];
        }

        // Number constraints
        if (isset($definition['maximum'])) {
            $constraints[] = '@constraint Maximum: '.$definition['maximum'];
        }

        if (isset($definition['minimum'])) {
            $constraints[] = '@constraint Minimum: '.$definition['minimum'];
        }

        // Array constraints
        if (isset($definition['maxItems'])) {
            $constraints[] = '@constraint Max items: '.$definition['maxItems'];
        }

        if (isset($definition['minItems'])) {
            $constraints[] = '@constraint Min items: '.$definition['minItems'];
        }

        // Enum constraints
        if (isset($definition['enum'])) {
            $values = is_array($definition['enum']) ? implode(', ', $definition['enum']) : $definition['enum'];
            $constraints[] = '@constraint Enum: '.$values;
        }

        // Format constraints
        if (isset($definition['format'])) {
            $constraints[] = '@constraint Format: '.$definition['format'];
        }

        // Const constraint
        if (isset($definition['const'])) {
            $value = is_bool($definition['const']) ? ($definition['const'] ? 'true' : 'false') : $definition['const'];
            $constraints[] = '@constraint Const: '.$value;
        }

        return $constraints;
    }

    /**
     * Generate a simple docblock.
     */
    public function generateSimple(string $description): string
    {
        return "    /**\n     * {$description}\n     */";
    }

    /**
     * Generate a one-line docblock.
     */
    public function generateOneLine(string $text): string
    {
        return "    /** {$text} */";
    }
}
