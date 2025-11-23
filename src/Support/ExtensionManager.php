<?php

namespace SocialDept\Schema\Support;

use Closure;

class ExtensionManager
{
    /**
     * Registered hooks.
     *
     * @var array<string, array<Closure>>
     */
    protected array $hooks = [];

    /**
     * Register a hook callback.
     */
    public function hook(string $name, Closure $callback): self
    {
        if (! isset($this->hooks[$name])) {
            $this->hooks[$name] = [];
        }

        $this->hooks[$name][] = $callback;

        return $this;
    }

    /**
     * Execute all hooks for a given name.
     *
     * @return array<mixed>
     */
    public function execute(string $name, mixed ...$args): array
    {
        if (! isset($this->hooks[$name])) {
            return [];
        }

        $results = [];

        foreach ($this->hooks[$name] as $callback) {
            $results[] = $callback(...$args);
        }

        return $results;
    }

    /**
     * Execute hooks and return the first non-null result.
     */
    public function executeUntil(string $name, mixed ...$args): mixed
    {
        if (! isset($this->hooks[$name])) {
            return null;
        }

        foreach ($this->hooks[$name] as $callback) {
            $result = $callback(...$args);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Execute hooks with a value that can be modified by each hook.
     */
    public function filter(string $name, mixed $value, mixed ...$args): mixed
    {
        if (! isset($this->hooks[$name])) {
            return $value;
        }

        foreach ($this->hooks[$name] as $callback) {
            $value = $callback($value, ...$args);
        }

        return $value;
    }

    /**
     * Check if a hook has any callbacks registered.
     */
    public function has(string $name): bool
    {
        return isset($this->hooks[$name]) && count($this->hooks[$name]) > 0;
    }

    /**
     * Get all callbacks for a hook.
     *
     * @return array<Closure>
     */
    public function get(string $name): array
    {
        return $this->hooks[$name] ?? [];
    }

    /**
     * Remove all callbacks for a hook.
     */
    public function remove(string $name): self
    {
        unset($this->hooks[$name]);

        return $this;
    }

    /**
     * Clear all hooks.
     */
    public function clear(): self
    {
        $this->hooks = [];

        return $this;
    }

    /**
     * Get count of callbacks for a hook.
     */
    public function count(string $name): int
    {
        return count($this->hooks[$name] ?? []);
    }

    /**
     * Get all registered hook names.
     *
     * @return array<string>
     */
    public function names(): array
    {
        return array_keys($this->hooks);
    }
}
