<?php

namespace SocialDept\Schema\Services;

use SocialDept\Schema\Contracts\Transformer;
use SocialDept\Schema\Exceptions\SchemaException;

class ModelMapper
{
    /**
     * Registered transformers.
     *
     * @var array<string, Transformer>
     */
    protected array $transformers = [];

    /**
     * Register a transformer for a specific type.
     */
    public function register(string $type, Transformer $transformer): self
    {
        $this->transformers[$type] = $transformer;

        return $this;
    }

    /**
     * Register multiple transformers at once.
     *
     * @param  array<string, Transformer>  $transformers
     */
    public function registerMany(array $transformers): self
    {
        foreach ($transformers as $type => $transformer) {
            $this->register($type, $transformer);
        }

        return $this;
    }

    /**
     * Transform raw data to model.
     */
    public function fromArray(string $type, array $data): mixed
    {
        $transformer = $this->getTransformer($type);

        if ($transformer === null) {
            throw SchemaException::withContext(
                "No transformer registered for type '{$type}'",
                ['type' => $type]
            );
        }

        return $transformer->fromArray($data);
    }

    /**
     * Transform model to raw data.
     */
    public function toArray(string $type, mixed $model): array
    {
        $transformer = $this->getTransformer($type);

        if ($transformer === null) {
            throw SchemaException::withContext(
                "No transformer registered for type '{$type}'",
                ['type' => $type]
            );
        }

        return $transformer->toArray($model);
    }

    /**
     * Transform multiple items from arrays.
     *
     * @param  array<array>  $items
     * @return array<mixed>
     */
    public function fromArrayMany(string $type, array $items): array
    {
        return array_map(
            fn (array $item) => $this->fromArray($type, $item),
            $items
        );
    }

    /**
     * Transform multiple items to arrays.
     *
     * @param  array<mixed>  $items
     * @return array<array>
     */
    public function toArrayMany(string $type, array $items): array
    {
        return array_map(
            fn (mixed $item) => $this->toArray($type, $item),
            $items
        );
    }

    /**
     * Get transformer for a specific type.
     */
    public function getTransformer(string $type): ?Transformer
    {
        // Check exact match first
        if (isset($this->transformers[$type])) {
            return $this->transformers[$type];
        }

        // Check if any transformer supports this type
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($type)) {
                return $transformer;
            }
        }

        return null;
    }

    /**
     * Check if a transformer is registered for a type.
     */
    public function has(string $type): bool
    {
        return $this->getTransformer($type) !== null;
    }

    /**
     * Unregister a transformer.
     */
    public function unregister(string $type): self
    {
        unset($this->transformers[$type]);

        return $this;
    }

    /**
     * Get all registered transformers.
     *
     * @return array<string, Transformer>
     */
    public function all(): array
    {
        return $this->transformers;
    }

    /**
     * Clear all registered transformers.
     */
    public function clear(): self
    {
        $this->transformers = [];

        return $this;
    }

    /**
     * Try to transform from array, return null if transformer not found.
     */
    public function tryFromArray(string $type, array $data): mixed
    {
        if (! $this->has($type)) {
            return null;
        }

        return $this->fromArray($type, $data);
    }

    /**
     * Try to transform to array, return null if transformer not found.
     */
    public function tryToArray(string $type, mixed $model): ?array
    {
        if (! $this->has($type)) {
            return null;
        }

        return $this->toArray($type, $model);
    }

    /**
     * Get count of registered transformers.
     */
    public function count(): int
    {
        return count($this->transformers);
    }
}
