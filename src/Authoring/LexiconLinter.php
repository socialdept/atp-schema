<?php

namespace SocialDept\AtpSchema\Authoring;

use SocialDept\AtpSchema\Parser\DefaultLexiconParser;
use SocialDept\AtpSupport\Nsid;
use Throwable;

/**
 * Checks a lexicon file for errors and for departures from the spec's style
 * guidance. The rules and their names follow Bluesky's reference linter
 * (indigo's lexlint), so `schema:lint` and `goat lex lint` agree.
 */
class LexiconLinter
{
    /**
     * Definition types that must be the file's main definition.
     */
    public const PRIMARY = ['record', 'query', 'procedure', 'subscription', 'permission-set'];

    /**
     * Strings or bytes longer than this belong in a blob.
     */
    public const LARGE = 20 * 1024;

    /**
     * Every definition type the language has.
     */
    public const TYPES = [
        'record', 'query', 'procedure', 'subscription', 'permission-set', 'permission',
        'null', 'boolean', 'integer', 'string', 'bytes', 'cid-link', 'blob',
        'array', 'object', 'params', 'token', 'ref', 'union', 'unknown',
    ];

    public function __construct(
        protected DefaultLexiconParser $parser = new DefaultLexiconParser(),
    ) {
    }

    /**
     * @param  array<string, mixed>  $lexicon
     * @return array<int, Issue>
     */
    public function lint(array $lexicon): array
    {
        $nsid = (string) ($lexicon['id'] ?? '');
        $issues = [];

        if (! Nsid::isValid($nsid)) {
            $issues[] = Issue::error($nsid, 'invalid-nsid', "the id is not a valid NSID: {$nsid}");
        }

        if (($lexicon['lexicon'] ?? null) !== 1) {
            $issues[] = Issue::error($nsid, 'lexicon-version', 'unsupported lexicon version: '.json_encode($lexicon['lexicon'] ?? null));

            return $issues;
        }

        if (! is_array($lexicon['defs'] ?? null) || $lexicon['defs'] === []) {
            $issues[] = Issue::error($nsid, 'lexicon-schema', 'the lexicon has no defs');
        }

        foreach ((array) ($lexicon['defs'] ?? []) as $defName => $def) {
            foreach (self::structure(is_array($def) ? $def : null, (string) $defName) as $problem) {
                $issues[] = Issue::error($nsid, 'lexicon-schema', $problem);
            }
        }

        try {
            $this->parser->parseArray($lexicon);
        } catch (Throwable $e) {
            $issues[] = Issue::error($nsid, 'lexicon-schema', $e->getMessage());
        }

        $name = Nsid::isValid($nsid) ? Nsid::parse($nsid)->getName() : '';

        foreach ((array) ($lexicon['defs'] ?? []) as $defName => $def) {
            if (is_array($def)) {
                array_push($issues, ...$this->def($nsid, $name, (string) $defName, $def));
            }
        }

        return $issues;
    }

    /**
     * @param  array<string, mixed>  $def
     * @return array<int, Issue>
     */
    protected function def(string $nsid, string $nsidName, string $defName, array $def): array
    {
        $issues = [];
        $type = $def['type'] ?? null;

        if (($syntax = self::nameProblem($defName)) !== null) {
            $issues[] = Issue::warn($nsid, 'def-name-syntax', "{$syntax}: {$defName}");
        }

        if ($nsidName === 'defs' && $defName === 'main') {
            $issues[] = Issue::warn($nsid, 'defs-main-definition', 'a defs file should not have a main definition');
        }

        if (in_array($type, self::PRIMARY, true)) {
            if ($defName !== 'main') {
                $issues[] = Issue::error($nsid, 'non-main-primary', "a {$type} must be the main definition, not {$defName}");
            }

            if ($type !== 'permission-set' && trim((string) ($def['description'] ?? '')) === '') {
                $issues[] = Issue::warn($nsid, 'missing-primary-description', "the {$type} has no description");
            }
        }

        switch ($type) {
            case 'record':
                array_push($issues, ...$this->recurse($nsid, $def['record'] ?? null));

                break;

            case 'query':
            case 'procedure':
                array_push($issues, ...$this->recurse($nsid, $def['parameters'] ?? null));

                if ($type === 'procedure') {
                    array_push($issues, ...$this->body($nsid, 'input', $def['input'] ?? null));
                }

                if (! isset($def['output'])) {
                    $issues[] = Issue::warn($nsid, 'endpoint-output-undefined', "the {$type} defines no output, even an empty one");
                } else {
                    array_push($issues, ...$this->body($nsid, 'output', $def['output']));
                }

                break;

            case 'subscription':
                array_push($issues, ...$this->recurse($nsid, $def['parameters'] ?? null));

                if (($def['message']['schema']['refs'] ?? []) === []) {
                    $issues[] = Issue::warn($nsid, 'subscription-no-messages', 'the subscription defines no message types');
                }

                break;

            case 'permission-set':
                if (trim((string) ($def['title'] ?? '')) === '') {
                    $issues[] = Issue::warn($nsid, 'permissionset-no-title', 'the permission set has no title');
                }

                if (($def['permissions'] ?? []) === []) {
                    $issues[] = Issue::warn($nsid, 'permissionset-no-permissions', 'the permission set defines no permissions');
                }

                break;

            default:
                array_push($issues, ...$this->recurse($nsid, $def));
        }

        return $issues;
    }

    /**
     * @return array<int, Issue>
     */
    protected function body(string $nsid, string $which, mixed $body): array
    {
        if (! is_array($body)) {
            return [];
        }

        $issues = [];

        if (trim((string) ($body['encoding'] ?? '')) === '') {
            $issues[] = Issue::warn($nsid, 'unspecified-encoding', "the {$which} body has no encoding");
        }

        array_push($issues, ...$this->recurse($nsid, $body['schema'] ?? null));

        return $issues;
    }

    /**
     * @return array<int, Issue>
     */
    protected function recurse(string $nsid, mixed $def): array
    {
        if (! is_array($def)) {
            return [];
        }

        $issues = [];

        switch ($def['type'] ?? null) {
            case 'string':
                if (($def['maxLength'] ?? 0) > self::LARGE) {
                    $issues[] = Issue::warn($nsid, 'large-string', 'a string field allows more than 20 KB, use a blob instead');
                }

                if (! isset($def['format']) && ! isset($def['maxLength']) && ! isset($def['maxGraphemes'])) {
                    $issues[] = Issue::info($nsid, 'unlimited-string', 'a string field has no format or maximum size');
                }

                foreach ((array) ($def['knownValues'] ?? []) as $value) {
                    if (is_string($value) && str_starts_with($value, '#')) {
                        $issues[] = Issue::warn($nsid, 'known-string-local-ref', "a knownValues entry looks like a local reference: {$value}");
                    }
                }

                break;

            case 'bytes':
                if (! isset($def['maxLength'])) {
                    $issues[] = Issue::info($nsid, 'unlimited-bytes', 'a bytes field has no maximum size');
                } elseif ($def['maxLength'] > self::LARGE) {
                    $issues[] = Issue::warn($nsid, 'large-bytes', 'a bytes field allows more than 20 KB, use a blob instead');
                }

                break;

            case 'array':
                array_push($issues, ...$this->recurse($nsid, $def['items'] ?? null));

                break;

            case 'object':
            case 'params':
                foreach ((array) ($def['properties'] ?? []) as $field => $property) {
                    array_push($issues, ...$this->recurse($nsid, $property));

                    if (($syntax = self::nameProblem((string) $field)) !== null) {
                        $issues[] = Issue::warn($nsid, 'field-name-syntax', "{$syntax}: {$field}");
                    }
                }

                foreach ((array) ($def['nullable'] ?? []) as $field) {
                    if (! in_array($field, (array) ($def['required'] ?? []), true)) {
                        $issues[] = Issue::warn($nsid, 'nullable-and-optional', "a field should not be both optional and nullable: {$field}");
                    }
                }

                break;

            case 'token':
                if (trim((string) ($def['description'] ?? '')) === '') {
                    $issues[] = Issue::warn($nsid, 'undescribed-token', 'a token has no description');
                }

                break;
        }

        return $issues;
    }

    /**
     * Basic shape problems in one definition and everything inside it: an
     * unknown type, or a field its type requires.
     *
     * @return array<int, string>
     */
    protected static function structure(?array $def, string $path): array
    {
        if ($def === null) {
            return ["{$path} is not an object"];
        }

        $type = $def['type'] ?? null;

        if (! in_array($type, self::TYPES, true)) {
            return ["{$path} has an unknown type: ".json_encode($type)];
        }

        $problems = [];
        $require = function (string $field, string $kind) use ($def, $path, $type, &$problems) {
            $ok = match ($kind) {
                'object' => is_array($def[$field] ?? null) && ! array_is_list($def[$field]),
                'list' => is_array($def[$field] ?? null) && array_is_list($def[$field]),
                default => is_string($def[$field] ?? null) && $def[$field] !== '',
            };

            if (! $ok) {
                $problems[] = "{$path} is a {$type} without {$field}";
            }
        };

        $children = [];

        switch ($type) {
            case 'record':
                $require('key', 'string');
                $require('record', 'object');
                $children['record'] = $def['record'] ?? null;

                break;
            case 'query':
            case 'procedure':
            case 'subscription':
                $children['parameters'] = $def['parameters'] ?? null;
                $children['input'] = $def['input']['schema'] ?? null;
                $children['output'] = $def['output']['schema'] ?? null;

                break;
            case 'array':
                $require('items', 'object');
                $children['items'] = $def['items'] ?? null;

                break;
            case 'object':
            case 'params':
                if (isset($def['properties']) && (! is_array($def['properties']) || (array_is_list($def['properties']) && $def['properties'] !== []))) {
                    $problems[] = "{$path} has properties that are not an object";
                }

                foreach ((array) ($def['properties'] ?? []) as $field => $property) {
                    $children["properties.{$field}"] = is_array($property) ? $property : [];
                }

                break;
            case 'ref':
                $require('ref', 'string');

                break;
            case 'union':
                $require('refs', 'list');

                break;
        }

        foreach ($children as $name => $child) {
            if ($child !== null) {
                array_push($problems, ...self::structure(is_array($child) ? $child : null, "{$path}.{$name}"));
            }
        }

        return $problems;
    }

    /**
     * What is wrong with a definition or field name, if anything.
     */
    public static function nameProblem(string $name): ?string
    {
        return match (true) {
            $name === '' => 'empty name',
            strlen($name) > 255 => 'name is longer than 255 characters',
            in_array($name, ['type', 'id'], true) => 'name is a commonly reserved keyword',
            preg_match('/^[a-zA-Z][a-zA-Z0-9]{0,255}$/', $name) !== 1 => 'name does not follow the recommended characters',
            default => null,
        };
    }
}
