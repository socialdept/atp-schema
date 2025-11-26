<?php

namespace SocialDept\AtpSchema\Data\Types;

use SocialDept\AtpSchema\Data\TypeDefinition;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class StringType extends TypeDefinition
{
    /**
     * Minimum string length in bytes.
     */
    public readonly ?int $minLength;

    /**
     * Maximum string length in bytes.
     */
    public readonly ?int $maxLength;

    /**
     * Minimum string length in graphemes.
     */
    public readonly ?int $minGraphemes;

    /**
     * Maximum string length in graphemes.
     */
    public readonly ?int $maxGraphemes;

    /**
     * String format (e.g., datetime, uri, at-uri, did, handle, at-identifier, nsid, cid, language).
     */
    public readonly ?string $format;

    /**
     * Allowed enum values.
     *
     * @var array<string>|null
     */
    public readonly ?array $enum;

    /**
     * Constant value.
     */
    public readonly ?string $const;

    /**
     * Known values (for documentation/hints, not validation).
     *
     * @var array<string>|null
     */
    public readonly ?array $knownValues;

    /**
     * Create a new StringType.
     *
     * @param  array<string>|null  $enum
     * @param  array<string>|null  $knownValues
     */
    public function __construct(
        ?string $description = null,
        ?int $minLength = null,
        ?int $maxLength = null,
        ?int $minGraphemes = null,
        ?int $maxGraphemes = null,
        ?string $format = null,
        ?array $enum = null,
        ?string $const = null,
        ?array $knownValues = null
    ) {
        parent::__construct('string', $description);

        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->minGraphemes = $minGraphemes;
        $this->maxGraphemes = $maxGraphemes;
        $this->format = $format;
        $this->enum = $enum;
        $this->const = $const;
        $this->knownValues = $knownValues;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'] ?? null,
            minLength: $data['minLength'] ?? null,
            maxLength: $data['maxLength'] ?? null,
            minGraphemes: $data['minGraphemes'] ?? null,
            maxGraphemes: $data['maxGraphemes'] ?? null,
            format: $data['format'] ?? null,
            enum: $data['enum'] ?? null,
            const: $data['const'] ?? null,
            knownValues: $data['knownValues'] ?? null
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $array = ['type' => $this->type];

        if ($this->description !== null) {
            $array['description'] = $this->description;
        }

        if ($this->minLength !== null) {
            $array['minLength'] = $this->minLength;
        }

        if ($this->maxLength !== null) {
            $array['maxLength'] = $this->maxLength;
        }

        if ($this->minGraphemes !== null) {
            $array['minGraphemes'] = $this->minGraphemes;
        }

        if ($this->maxGraphemes !== null) {
            $array['maxGraphemes'] = $this->maxGraphemes;
        }

        if ($this->format !== null) {
            $array['format'] = $this->format;
        }

        if ($this->enum !== null) {
            $array['enum'] = $this->enum;
        }

        if ($this->const !== null) {
            $array['const'] = $this->const;
        }

        if ($this->knownValues !== null) {
            $array['knownValues'] = $this->knownValues;
        }

        return $array;
    }

    /**
     * Validate a value against this type definition.
     */
    public function validate(mixed $value, string $path = ''): void
    {
        if (! is_string($value)) {
            throw RecordValidationException::invalidType($path, 'string', gettype($value));
        }

        // Const validation
        if ($this->const !== null && $value !== $this->const) {
            throw RecordValidationException::invalidValue($path, "must equal '{$this->const}'");
        }

        // Enum validation
        if ($this->enum !== null && ! in_array($value, $this->enum, true)) {
            throw RecordValidationException::invalidValue($path, 'must be one of: '.implode(', ', $this->enum));
        }

        // Length validation (bytes)
        $length = strlen($value);

        if ($this->minLength !== null && $length < $this->minLength) {
            throw RecordValidationException::invalidValue($path, "must be at least {$this->minLength} bytes");
        }

        if ($this->maxLength !== null && $length > $this->maxLength) {
            throw RecordValidationException::invalidValue($path, "must be at most {$this->maxLength} bytes");
        }

        // Grapheme validation
        if ($this->minGraphemes !== null || $this->maxGraphemes !== null) {
            $graphemes = grapheme_strlen($value);

            if ($this->minGraphemes !== null && $graphemes < $this->minGraphemes) {
                throw RecordValidationException::invalidValue($path, "must be at least {$this->minGraphemes} graphemes");
            }

            if ($this->maxGraphemes !== null && $graphemes > $this->maxGraphemes) {
                throw RecordValidationException::invalidValue($path, "must be at most {$this->maxGraphemes} graphemes");
            }
        }

        // Format validation
        if ($this->format !== null) {
            $this->validateFormat($value, $path);
        }
    }

    /**
     * Validate string format.
     */
    protected function validateFormat(string $value, string $path): void
    {
        switch ($this->format) {
            case 'datetime':
                if (! $this->isValidDatetime($value)) {
                    throw RecordValidationException::invalidValue($path, 'must be a valid ISO 8601 datetime');
                }

                break;

            case 'uri':
                if (! filter_var($value, FILTER_VALIDATE_URL)) {
                    throw RecordValidationException::invalidValue($path, 'must be a valid URI');
                }

                break;

            case 'at-uri':
                if (! str_starts_with($value, 'at://')) {
                    throw RecordValidationException::invalidValue($path, 'must be a valid AT URI');
                }

                break;

            case 'did':
                if (! str_starts_with($value, 'did:')) {
                    throw RecordValidationException::invalidValue($path, 'must be a valid DID');
                }

                break;

            case 'handle':
                if (! $this->isValidHandle($value)) {
                    throw RecordValidationException::invalidValue($path, 'must be a valid handle');
                }

                break;

            case 'at-identifier':
                // Can be either DID or handle
                if (! str_starts_with($value, 'did:') && ! $this->isValidHandle($value)) {
                    throw RecordValidationException::invalidValue($path, 'must be a valid AT identifier (DID or handle)');
                }

                break;

            case 'nsid':
                if (! preg_match('/^[a-zA-Z]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/', $value)) {
                    throw RecordValidationException::invalidValue($path, 'must be a valid NSID');
                }

                break;

            case 'cid':
                // Basic CID validation (starts with proper characters)
                if (! preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    throw RecordValidationException::invalidValue($path, 'must be a valid CID');
                }

                break;

            case 'language':
                // Basic language tag validation (BCP 47)
                if (! preg_match('/^[a-z]{2,3}(-[A-Z][a-z]{3})?(-[A-Z]{2})?$/', $value)) {
                    throw RecordValidationException::invalidValue($path, 'must be a valid language tag');
                }

                break;
        }
    }

    /**
     * Check if value is a valid ISO 8601 datetime.
     */
    protected function isValidDatetime(string $value): bool
    {
        try {
            new \DateTimeImmutable($value);

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Check if value is a valid handle.
     */
    protected function isValidHandle(string $value): bool
    {
        // Basic handle validation: domain-like format
        return (bool) preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/', $value);
    }
}
