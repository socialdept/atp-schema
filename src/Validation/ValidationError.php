<?php

namespace SocialDept\AtpSchema\Validation;

use JsonSerializable;

class ValidationError implements JsonSerializable
{
    /**
     * Tracks which optional values were explicitly set.
     */
    protected bool $hasExpectedValue = false;

    protected bool $hasActualValue = false;

    /**
     * Create a new ValidationError.
     */
    public function __construct(
        public readonly string $field,
        public readonly string $message,
        public readonly ?string $rule = null,
        mixed $expected = null,
        mixed $actual = null,
        public readonly array $context = []
    ) {
        $this->hasExpectedValue = func_num_args() >= 4;
        $this->hasActualValue = func_num_args() >= 5;

        // Use object properties for values that can be null
        $this->expected = $expected;
        $this->actual = $actual;
    }

    public readonly mixed $expected;

    public readonly mixed $actual;

    /**
     * Create from field and message.
     */
    public static function make(string $field, string $message): self
    {
        return new self($field, $message);
    }

    /**
     * Create with rule context.
     */
    public static function withRule(string $field, string $message, string $rule): self
    {
        return new self($field, $message, $rule);
    }

    /**
     * Create with full context.
     */
    public static function withContext(
        string $field,
        string $message,
        string $rule,
        mixed $expected = null,
        mixed $actual = null,
        array $context = []
    ): self {
        return new self($field, $message, $rule, $expected, $actual, $context);
    }

    /**
     * Get the field path.
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get the error message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the validation rule that failed.
     */
    public function getRule(): ?string
    {
        return $this->rule;
    }

    /**
     * Get the expected value.
     */
    public function getExpected(): mixed
    {
        return $this->expected;
    }

    /**
     * Get the actual value.
     */
    public function getActual(): mixed
    {
        return $this->actual;
    }

    /**
     * Get additional context.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Check if error has rule information.
     */
    public function hasRule(): bool
    {
        return $this->rule !== null;
    }

    /**
     * Check if error has expected value.
     */
    public function hasExpected(): bool
    {
        return $this->hasExpectedValue;
    }

    /**
     * Check if error has actual value.
     */
    public function hasActual(): bool
    {
        return $this->hasActualValue;
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'field' => $this->field,
            'message' => $this->message,
        ];

        if ($this->rule !== null) {
            $data['rule'] = $this->rule;
        }

        if ($this->hasExpectedValue) {
            $data['expected'] = $this->expected;
        }

        if ($this->hasActualValue) {
            $data['actual'] = $this->actual;
        }

        if (! empty($this->context)) {
            $data['context'] = $this->context;
        }

        return $data;
    }

    /**
     * Convert to JSON.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return "{$this->field}: {$this->message}";
    }
}
