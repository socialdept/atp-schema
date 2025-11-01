<?php

namespace SocialDept\Schema\Data;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use Stringable;

abstract class Data implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    /**
     * Get the lexicon NSID for this data type.
     */
    abstract public static function getLexicon(): string;

    /**
     * Convert the data to an array.
     */
    public function toArray(): array
    {
        $result = [];

        foreach (get_object_vars($this) as $property => $value) {
            $result[$property] = $this->serializeValue($value);
        }

        return $result;
    }

    /**
     * Convert the data to JSON.
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the data for JSON serialization.
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * Convert the data to a string.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /**
     * Serialize a value for output.
     */
    protected function serializeValue(mixed $value): mixed
    {
        if ($value instanceof self) {
            return $value->toArray();
        }

        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->serializeValue($item), $value);
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTimeInterface::ATOM);
        }

        return $value;
    }

    /**
     * Create an instance from an array.
     */
    abstract public static function fromArray(array $data): static;

    /**
     * Create an instance from JSON.
     */
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: '.json_last_error_msg());
        }

        return static::fromArray($data);
    }

    /**
     * Create an instance from an AT Protocol record.
     *
     * This is an alias for fromArray for semantic clarity
     * when working with AT Protocol records.
     */
    public static function fromRecord(array $record): static
    {
        return static::fromArray($record);
    }

    /**
     * Convert to an AT Protocol record.
     *
     * This is an alias for toArray for semantic clarity
     * when working with AT Protocol records.
     */
    public function toRecord(): array
    {
        return $this->toArray();
    }

    /**
     * Check if two data objects are equal.
     */
    public function equals(self $other): bool
    {
        if (! $other instanceof static) {
            return false;
        }

        return $this->toArray() === $other->toArray();
    }

    /**
     * Get a hash of the data.
     */
    public function hash(): string
    {
        return hash('sha256', $this->toJson());
    }

    /**
     * Validate the data against its lexicon schema.
     */
    public function validate(): bool
    {
        if (! function_exists('schema_validate')) {
            return true;
        }

        try {
            return schema_validate(static::getLexicon(), $this->toArray());
        } catch (\Throwable) {
            return true;
        }
    }

    /**
     * Validate and get errors.
     *
     * @return array<string, array<string>>
     */
    public function validateWithErrors(): array
    {
        if (! function_exists('SocialDept\Schema\schema')) {
            return [];
        }

        try {
            return schema()->validateWithErrors(static::getLexicon(), $this->toArray());
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Get a property value dynamically.
     */
    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new \InvalidArgumentException("Property {$name} does not exist on ".static::class);
    }

    /**
     * Check if a property exists.
     */
    public function __isset(string $name): bool
    {
        return property_exists($this, $name);
    }

    /**
     * Clone the data with modified properties.
     */
    public function with(array $properties): static
    {
        $data = $this->toArray();

        foreach ($properties as $key => $value) {
            $data[$key] = $value;
        }

        return static::fromArray($data);
    }
}
