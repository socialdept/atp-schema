<?php

namespace SocialDept\AtpSchema\Validation;

use Illuminate\Support\Traits\Macroable;
use SocialDept\AtpSchema\Contracts\LexiconValidator as LexiconValidatorContract;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Exceptions\SchemaValidationException;
use SocialDept\AtpSchema\Parser\SchemaLoader;
use SocialDept\AtpSchema\Parser\TypeParser;

class Validator implements LexiconValidatorContract
{
    use Macroable;

    /**
     * Validation mode constants.
     */
    public const MODE_STRICT = 'strict';

    public const MODE_OPTIMISTIC = 'optimistic';

    public const MODE_LENIENT = 'lenient';

    /**
     * Schema loader for loading lexicon documents.
     */
    protected SchemaLoader $schemaLoader;

    /**
     * Type parser for parsing and resolving types.
     */
    protected TypeParser $typeParser;

    /**
     * Validation mode.
     */
    protected string $mode = self::MODE_STRICT;

    /**
     * Collected validation errors.
     *
     * @var array<string, array<string>>
     */
    protected array $errors = [];

    /**
     * Create a new Validator.
     */
    public function __construct(
        SchemaLoader $schemaLoader,
        ?TypeParser $typeParser = null
    ) {
        $this->schemaLoader = $schemaLoader;
        $this->typeParser = $typeParser ?? new TypeParser(schemaLoader: $schemaLoader);
    }

    /**
     * Validate data against Lexicon schema.
     */
    public function validate(array $data, LexiconDocument $schema): bool
    {
        $this->errors = [];

        try {
            $this->validateData($data, $schema);

            return empty($this->errors);
        } catch (RecordValidationException|SchemaValidationException) {
            return false;
        }
    }

    /**
     * Validate and return errors.
     *
     * @return array<string, array<string>>
     */
    public function validateWithErrors(array $data, LexiconDocument $schema): array
    {
        $this->errors = [];

        try {
            $this->validateData($data, $schema);

            return $this->errors;
        } catch (RecordValidationException $e) {
            return ['_root' => [$e->getMessage()]];
        } catch (SchemaValidationException $e) {
            return ['_schema' => [$e->getMessage()]];
        }
    }

    /**
     * Validate a specific field.
     */
    public function validateField(mixed $value, string $field, LexiconDocument $schema): bool
    {
        $this->errors = [];

        try {
            $mainDef = $schema->getMainDefinition();

            if ($mainDef === null) {
                return false;
            }

            $properties = $this->extractProperties($mainDef);

            if (! isset($properties[$field])) {
                if ($this->mode === self::MODE_STRICT) {
                    $this->addError($field, "Field '{$field}' is not defined in schema");

                    return false;
                }

                return true;
            }

            $this->validateProperty($value, $field, $properties[$field], $schema);

            return empty($this->errors);
        } catch (RecordValidationException) {
            return false;
        }
    }

    /**
     * Set validation mode (strict, optimistic, lenient).
     */
    public function setMode(string $mode): void
    {
        if (! in_array($mode, [self::MODE_STRICT, self::MODE_OPTIMISTIC, self::MODE_LENIENT])) {
            throw new \InvalidArgumentException("Invalid validation mode: {$mode}");
        }

        $this->mode = $mode;
    }

    /**
     * Get current validation mode.
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Validate data against schema.
     */
    protected function validateData(array $data, LexiconDocument $schema): void
    {
        $mainDef = $schema->getMainDefinition();

        if ($mainDef === null) {
            throw SchemaValidationException::invalidStructure(
                $schema->getNsid(),
                ['Missing main definition']
            );
        }

        $type = $mainDef['type'] ?? null;

        // Only validate if it's a record or object type
        if ($type !== 'record' && $type !== 'object') {
            throw SchemaValidationException::invalidStructure(
                $schema->getNsid(),
                ['Schema must be a record or object type, got: '.($type ?? 'unknown')]
            );
        }

        $properties = $this->extractProperties($mainDef);
        $required = $this->extractRequired($mainDef);

        // Validate required fields
        $this->validateRequired($data, $required);

        // Validate defined properties
        foreach ($properties as $name => $propDef) {
            if (array_key_exists($name, $data)) {
                $this->validateProperty($data[$name], $name, $propDef, $schema);
            }
        }

        // Check for unknown fields
        if ($this->mode === self::MODE_STRICT) {
            $this->validateNoUnknownFields($data, array_keys($properties));
        }
    }

    /**
     * Extract properties from definition.
     *
     * @param  array<string, mixed>  $definition
     * @return array<string, array<string, mixed>>
     */
    protected function extractProperties(array $definition): array
    {
        // Handle record type
        if (isset($definition['record']) && is_array($definition['record'])) {
            return $definition['record']['properties'] ?? [];
        }

        // Handle object type
        if ($definition['type'] === 'object' || isset($definition['properties'])) {
            return $definition['properties'] ?? [];
        }

        return [];
    }

    /**
     * Extract required fields from definition.
     *
     * @param  array<string, mixed>  $definition
     * @return array<string>
     */
    protected function extractRequired(array $definition): array
    {
        // Handle record type
        if (isset($definition['record']) && is_array($definition['record'])) {
            return $definition['record']['required'] ?? [];
        }

        // Handle object type
        return $definition['required'] ?? [];
    }

    /**
     * Validate required fields are present.
     *
     * @param  array<string>  $required
     */
    protected function validateRequired(array $data, array $required): void
    {
        if ($this->mode === self::MODE_LENIENT) {
            return;
        }

        foreach ($required as $field) {
            if (! array_key_exists($field, $data)) {
                $this->addError($field, "Required field '{$field}' is missing");
            }
        }
    }

    /**
     * Validate a single property.
     *
     * @param  array<string, mixed>  $propDef
     */
    protected function validateProperty(mixed $value, string $name, array $propDef, LexiconDocument $schema): void
    {
        try {
            $type = $propDef['type'] ?? 'unknown';

            // Basic type validation
            $this->validateType($value, $type, $name);

            // Constraint validation (skip in lenient mode)
            if ($this->mode !== self::MODE_LENIENT) {
                $this->validateConstraints($value, $propDef, $name);
            }

            // Nested object validation
            if ($type === 'object' && is_array($value)) {
                $this->validateNestedObject($value, $propDef, $name, $schema);
            }

            // Array validation
            if ($type === 'array' && is_array($value)) {
                $this->validateArray($value, $propDef, $name, $schema);
            }
        } catch (RecordValidationException $e) {
            $this->addError($name, $e->getMessage());
        }
    }

    /**
     * Validate value type.
     */
    protected function validateType(mixed $value, string $expectedType, string $fieldName): void
    {
        $actualType = gettype($value);

        $valid = match ($expectedType) {
            'string' => is_string($value),
            'integer' => is_int($value),
            'boolean' => is_bool($value),
            'number' => is_numeric($value),
            'object' => is_array($value),
            'array' => is_array($value),
            'null' => is_null($value),
            default => true, // Unknown types pass in optimistic/lenient modes
        };

        if (! $valid) {
            $this->addError($fieldName, "Expected type '{$expectedType}', got '{$actualType}'");
        }
    }

    /**
     * Validate field constraints.
     *
     * @param  array<string, mixed>  $propDef
     */
    protected function validateConstraints(mixed $value, array $propDef, string $fieldName): void
    {
        // String length constraints
        if (is_string($value)) {
            if (isset($propDef['maxLength']) && strlen($value) > $propDef['maxLength']) {
                $this->addError($fieldName, "String exceeds maximum length of {$propDef['maxLength']}");
            }

            if (isset($propDef['minLength']) && strlen($value) < $propDef['minLength']) {
                $this->addError($fieldName, "String is shorter than minimum length of {$propDef['minLength']}");
            }

            if (isset($propDef['maxGraphemes']) && grapheme_strlen($value) > $propDef['maxGraphemes']) {
                $this->addError($fieldName, "String exceeds maximum graphemes of {$propDef['maxGraphemes']}");
            }

            if (isset($propDef['minGraphemes']) && grapheme_strlen($value) < $propDef['minGraphemes']) {
                $this->addError($fieldName, "String has fewer than minimum graphemes of {$propDef['minGraphemes']}");
            }
        }

        // Number constraints
        if (is_numeric($value)) {
            if (isset($propDef['maximum']) && $value > $propDef['maximum']) {
                $this->addError($fieldName, "Value exceeds maximum of {$propDef['maximum']}");
            }

            if (isset($propDef['minimum']) && $value < $propDef['minimum']) {
                $this->addError($fieldName, "Value is less than minimum of {$propDef['minimum']}");
            }
        }

        // Array constraints
        if (is_array($value)) {
            $count = count($value);

            if (isset($propDef['maxItems']) && $count > $propDef['maxItems']) {
                $this->addError($fieldName, "Array exceeds maximum items of {$propDef['maxItems']}");
            }

            if (isset($propDef['minItems']) && $count < $propDef['minItems']) {
                $this->addError($fieldName, "Array has fewer than minimum items of {$propDef['minItems']}");
            }
        }

        // Enum constraint
        if (isset($propDef['enum']) && ! in_array($value, $propDef['enum'], true)) {
            $allowedValues = implode(', ', $propDef['enum']);
            $this->addError($fieldName, "Value must be one of: {$allowedValues}");
        }

        // Const constraint
        if (isset($propDef['const']) && $value !== $propDef['const']) {
            $expectedValue = json_encode($propDef['const']);
            $this->addError($fieldName, "Value must be {$expectedValue}");
        }
    }

    /**
     * Validate nested object.
     *
     * @param  array<string, mixed>  $propDef
     */
    protected function validateNestedObject(array $value, array $propDef, string $fieldName, LexiconDocument $schema): void
    {
        $nestedProperties = $propDef['properties'] ?? [];
        $nestedRequired = $propDef['required'] ?? [];

        // Create temporary document for nested validation
        $nestedDoc = new LexiconDocument(
            lexicon: 1,
            id: $schema->id,
            defs: ['main' => [
                'type' => 'object',
                'properties' => $nestedProperties,
                'required' => $nestedRequired,
            ]],
            description: null,
            source: null,
            raw: []
        );

        $originalErrors = $this->errors;
        $this->errors = [];

        $this->validateData($value, $nestedDoc);

        // Prefix nested errors with field name
        foreach ($this->errors as $nestedField => $messages) {
            foreach ($messages as $message) {
                $this->addError("{$fieldName}.{$nestedField}", $message);
            }
        }

        $this->errors = array_merge($originalErrors, $this->errors);
    }

    /**
     * Validate array items.
     *
     * @param  array<string, mixed>  $propDef
     */
    protected function validateArray(array $value, array $propDef, string $fieldName, LexiconDocument $schema): void
    {
        if (! isset($propDef['items'])) {
            return;
        }

        $itemDef = $propDef['items'];

        foreach ($value as $index => $item) {
            $itemFieldName = "{$fieldName}[{$index}]";
            $this->validateProperty($item, $itemFieldName, $itemDef, $schema);
        }
    }

    /**
     * Validate no unknown fields are present.
     *
     * @param  array<string>  $allowedFields
     */
    protected function validateNoUnknownFields(array $data, array $allowedFields): void
    {
        foreach (array_keys($data) as $field) {
            if (! in_array($field, $allowedFields)) {
                $this->addError($field, "Unknown field '{$field}' is not allowed");
            }
        }
    }

    /**
     * Add a validation error.
     */
    protected function addError(string $field, string $message): void
    {
        if (! isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;
    }
}
