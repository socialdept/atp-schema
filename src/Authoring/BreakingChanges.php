<?php

namespace SocialDept\AtpSchema\Authoring;

/**
 * Finds the changes between two versions of a lexicon that would break the
 * data or the clients already relying on it.
 *
 * The spec lets a published lexicon grow but not change shape: new fields
 * must be optional, types cannot change, required fields cannot be removed,
 * and a closed union cannot gain or lose types. These rules follow Bluesky's
 * reference implementation (indigo's lexlint), finding for finding, so a
 * lexicon passes here exactly when it passes `goat lex breaking`.
 *
 * Adding or removing a whole definition or property is never flagged: an
 * optional field may come and go. Making it required is caught by the
 * required-fields check instead.
 */
class BreakingChanges
{
    /**
     * @param  array<string, mixed>  $before  The published version
     * @param  array<string, mixed>  $after  The version about to replace it
     * @return array<int, Issue>
     */
    public static function between(array $before, array $after): array
    {
        $nsid = (string) ($before['id'] ?? $after['id'] ?? '');

        return self::maps($nsid, '', self::map($before['defs'] ?? []), self::map($after['defs'] ?? []));
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return array<int, Issue>
     */
    protected static function maps(string $nsid, string $path, array $before, array $after): array
    {
        $issues = [];
        $names = array_keys($before + $after);
        sort($names, SORT_STRING);

        foreach ($names as $name) {
            if (! is_array($before[$name] ?? null) || ! is_array($after[$name] ?? null)) {
                continue;
            }

            array_push($issues, ...self::def($nsid, $path === '' ? (string) $name : "{$path}.{$name}", $before[$name], $after[$name]));
        }

        return $issues;
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return array<int, Issue>
     */
    protected static function def(string $nsid, string $name, array $before, array $after): array
    {
        $type = $before['type'] ?? null;

        if ($type !== ($after['type'] ?? null)) {
            return [Issue::error($nsid, 'type-change', sprintf('schema type changed (%s): %s to %s', $name, $type ?? 'none', $after['type'] ?? 'none'))];
        }

        $issues = [];

        switch ($type) {
            case 'record':
                if (($before['key'] ?? null) !== ($after['key'] ?? null)) {
                    $issues[] = Issue::error($nsid, 'record-key-type', sprintf('record key type changed (%s): %s to %s', $name, $before['key'] ?? 'none', $after['key'] ?? 'none'));
                }

                array_push($issues, ...self::nested($nsid, $name, $before['record'] ?? null, $after['record'] ?? null));

                break;

            case 'query':
            case 'procedure':
            case 'subscription':
                array_push($issues, ...self::nested($nsid, $name, $before['parameters'] ?? null, $after['parameters'] ?? null));

                foreach (['input', 'output'] as $body) {
                    array_push($issues, ...self::body($nsid, "{$name}.{$body}", $before[$body] ?? null, $after[$body] ?? null));
                }

                if ($type === 'subscription') {
                    array_push($issues, ...self::nested($nsid, "{$name}.message", $before['message']['schema'] ?? null, $after['message']['schema'] ?? null));
                }

                break;

            case 'boolean':
                $issues = self::constChange($nsid, $name, $before, $after);

                break;

            case 'integer':
                $issues = self::constChange($nsid, $name, $before, $after);

                if (! self::sameSet($before['enum'] ?? [], $after['enum'] ?? [])) {
                    $issues[] = Issue::error($nsid, 'enum-values', "integer enum values changed ({$name})");
                }

                if (! self::sameValues($before, $after, ['minimum', 'maximum'])) {
                    $issues[] = Issue::warn($nsid, 'integer-range', "integer minimum or maximum changed ({$name})");
                }

                break;

            case 'string':
                // A default and knownValues may change freely.
                $issues = self::constChange($nsid, $name, $before, $after);

                if (! self::sameSet($before['enum'] ?? [], $after['enum'] ?? [])) {
                    $issues[] = Issue::error($nsid, 'enum-values', "string enum values changed ({$name})");
                }

                if (! self::sameValues($before, $after, ['minLength', 'maxLength', 'minGraphemes', 'maxGraphemes'])) {
                    $issues[] = Issue::warn($nsid, 'string-length', "string length limits changed ({$name})");
                }

                if (($before['format'] ?? null) !== ($after['format'] ?? null)) {
                    $issues[] = Issue::error($nsid, 'string-format', "string format changed ({$name})");
                }

                break;

            case 'bytes':
                if (! self::sameValues($before, $after, ['minLength', 'maxLength'])) {
                    $issues[] = Issue::warn($nsid, 'bytes-length', "bytes length limits changed ({$name})");
                }

                break;

            case 'array':
                if (! self::sameValues($before, $after, ['minLength', 'maxLength'])) {
                    $issues[] = Issue::warn($nsid, 'array-length', "array length limits changed ({$name})");
                }

                array_push($issues, ...self::nested($nsid, "{$name}.items", $before['items'] ?? null, $after['items'] ?? null));

                break;

            case 'object':
            case 'params':
                if (! self::sameSet($before['required'] ?? [], $after['required'] ?? [])) {
                    $issues[] = Issue::error($nsid, $type === 'object' ? 'object-required' : 'params-required', "required fields changed ({$name})");
                }

                if ($type === 'object' && ! self::sameSet($before['nullable'] ?? [], $after['nullable'] ?? [])) {
                    $issues[] = Issue::error($nsid, 'object-nullable', "nullable fields changed ({$name})");
                }

                array_push($issues, ...self::maps($nsid, $name, self::map($before['properties'] ?? []), self::map($after['properties'] ?? [])));

                break;

            case 'blob':
                if (! self::sameSet($before['accept'] ?? [], $after['accept'] ?? [])) {
                    $issues[] = Issue::warn($nsid, 'blob-accept', "blob accepted content types changed ({$name})");
                }

                if (! self::sameValues($before, $after, ['maxSize'])) {
                    $issues[] = Issue::warn($nsid, 'blob-size', "blob maximum size changed ({$name})");
                }

                break;

            case 'ref':
                if (($before['ref'] ?? null) !== ($after['ref'] ?? null)) {
                    $issues[] = Issue::warn($nsid, 'ref-change', sprintf('referenced schema changed (%s): %s to %s', $name, $before['ref'] ?? 'none', $after['ref'] ?? 'none'));
                }

                break;

            case 'union':
                if (($before['closed'] ?? null) !== ($after['closed'] ?? null)) {
                    $issues[] = Issue::error($nsid, 'union-open-closed', "union changed between open and closed ({$name})");
                }

                if (($before['closed'] ?? false) === true && ! self::sameSet($before['refs'] ?? [], $after['refs'] ?? [])) {
                    $issues[] = Issue::error($nsid, 'union-closed-refs', "closed union types changed ({$name})");
                }

                break;
        }

        return $issues;
    }

    /**
     * An API body: its encoding and, when both have one, its schema.
     *
     * @return array<int, Issue>
     */
    protected static function body(string $nsid, string $name, mixed $before, mixed $after): array
    {
        if (! is_array($before) || ! is_array($after)) {
            return [];
        }

        $issues = [];

        if (($before['encoding'] ?? null) !== ($after['encoding'] ?? null)) {
            $issues[] = Issue::error($nsid, 'body-encoding', sprintf('body encoding changed (%s): %s to %s', $name, $before['encoding'] ?? 'none', $after['encoding'] ?? 'none'));
        }

        array_push($issues, ...self::nested($nsid, $name, $before['schema'] ?? null, $after['schema'] ?? null));

        return $issues;
    }

    /**
     * @return array<int, Issue>
     */
    protected static function nested(string $nsid, string $name, mixed $before, mixed $after): array
    {
        return is_array($before) && is_array($after) ? self::def($nsid, $name, $before, $after) : [];
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return array<int, Issue>
     */
    protected static function constChange(string $nsid, string $name, array $before, array $after): array
    {
        return self::sameValues($before, $after, ['const'])
            ? []
            : [Issue::error($nsid, 'const-value', "const value changed ({$name})")];
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @param  array<int, string>  $keys
     */
    protected static function sameValues(array $before, array $after, array $keys): bool
    {
        foreach ($keys as $key) {
            if (($before[$key] ?? null) !== ($after[$key] ?? null)) {
                return false;
            }
        }

        return true;
    }

    protected static function sameSet(mixed $before, mixed $after): bool
    {
        $before = array_values((array) $before);
        $after = array_values((array) $after);
        sort($before);
        sort($after);

        return $before === $after;
    }

    /**
     * @return array<string, mixed>
     */
    protected static function map(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }
}
