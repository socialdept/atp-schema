<?php

namespace SocialDept\AtpSchema\Authoring;

/**
 * What differs between two versions of a lexicon, path by path. Keys are
 * compared in any order, so reformatting a file is not a change.
 */
class LexiconDiff
{
    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return array<int, array{path: string, change: string, before: mixed, after: mixed}>
     */
    public static function between(array $before, array $after, string $path = ''): array
    {
        $changes = [];
        $keys = array_keys($before + $after);
        sort($keys, SORT_STRING);

        foreach ($keys as $key) {
            $at = $path === '' ? (string) $key : "{$path}.{$key}";
            $has = [array_key_exists($key, $before), array_key_exists($key, $after)];

            if ($has === [false, true]) {
                $changes[] = ['path' => $at, 'change' => 'added', 'before' => null, 'after' => $after[$key]];
            } elseif ($has === [true, false]) {
                $changes[] = ['path' => $at, 'change' => 'removed', 'before' => $before[$key], 'after' => null];
            } elseif (is_array($before[$key]) && is_array($after[$key]) && ! array_is_list($before[$key]) && ! array_is_list($after[$key])) {
                array_push($changes, ...self::between($before[$key], $after[$key], $at));
            } elseif (self::normalize($before[$key]) !== self::normalize($after[$key])) {
                $changes[] = ['path' => $at, 'change' => 'changed', 'before' => $before[$key], 'after' => $after[$key]];
            }
        }

        return $changes;
    }

    /**
     * Whether two versions say the same thing, ignoring key order and the
     * record's $type.
     *
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     */
    public static function same(array $before, array $after): bool
    {
        unset($before['$type'], $after['$type']);

        return self::normalize($before) === self::normalize($after);
    }

    protected static function normalize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $value = array_map(self::normalize(...), $value);

        if (! array_is_list($value)) {
            ksort($value, SORT_STRING);
        }

        return $value;
    }
}
