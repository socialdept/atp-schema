<?php

namespace SocialDept\AtpSchema\Support;

/**
 * String formats as the atproto Lexicon spec defines them, matching Bluesky's
 * reference implementation (indigo's atproto/syntax).
 */
final class Formats
{
    /**
     * Any URI with a scheme, per RFC 3986's generic syntax: https, at, did,
     * ipfs, dns and others. At most 8 KB, with no whitespace.
     */
    public static function isUri(string $value): bool
    {
        return strlen($value) <= 8192 && preg_match('/^[a-z][a-z.-]{0,80}:[[:graph:]]+$/', $value) === 1;
    }

    /**
     * A BCP 47 language tag, checked loosely: a primary subtag of two or
     * three letters (or i), then any subtags. At most 128 characters.
     */
    public static function isLanguage(string $value): bool
    {
        return strlen($value) <= 128 && preg_match('/^(i|[a-z]{2,3})(-[a-zA-Z0-9]+)*$/', $value) === 1;
    }
}
