<?php

namespace SocialDept\AtpSchema\Tests\Unit\Authoring;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SocialDept\AtpSchema\Authoring\BreakingChanges;
use SocialDept\AtpSchema\Authoring\Issue;

class BreakingChangesTest extends TestCase
{
    /**
     * A record with a bit of everything, to change one thing at a time.
     *
     * @return array<string, mixed>
     */
    protected static function record(): array
    {
        return [
            'lexicon' => 1,
            'id' => 'com.example.thing',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'required' => ['title', 'createdAt'],
                        'nullable' => [],
                        'properties' => [
                            'title' => ['type' => 'string', 'maxLength' => 300],
                            'kind' => ['type' => 'string', 'enum' => ['a', 'b']],
                            'link' => ['type' => 'string', 'format' => 'uri'],
                            'count' => ['type' => 'integer', 'minimum' => 0],
                            'flag' => ['type' => 'boolean', 'const' => true],
                            'tags' => ['type' => 'array', 'items' => ['type' => 'string', 'maxLength' => 64], 'maxLength' => 8],
                            'image' => ['type' => 'blob', 'accept' => ['image/*'], 'maxSize' => 1000000],
                            'embed' => ['type' => 'union', 'refs' => ['#a', '#b'], 'closed' => true],
                            'author' => ['type' => 'ref', 'ref' => 'com.example.defs#author'],
                            'createdAt' => ['type' => 'string', 'format' => 'datetime'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function query(): array
    {
        return [
            'lexicon' => 1,
            'id' => 'com.example.getThings',
            'defs' => [
                'main' => [
                    'type' => 'query',
                    'parameters' => ['type' => 'params', 'required' => ['actor'], 'properties' => ['actor' => ['type' => 'string']]],
                    'output' => ['encoding' => 'application/json', 'schema' => ['type' => 'object', 'properties' => ['cursor' => ['type' => 'string']]]],
                ],
            ],
        ];
    }

    /**
     * @param  array<int, string>  $names
     * @param  array<int, Issue>  $issues
     */
    protected function assertFound(array $names, array $issues): void
    {
        $found = array_map(fn (Issue $issue) => $issue->name, $issues);
        sort($found);
        sort($names);

        $this->assertSame($names, $found);
    }

    public function test_identical_versions_break_nothing(): void
    {
        $this->assertSame([], BreakingChanges::between(self::record(), self::record()));
    }

    public function test_an_optional_field_or_a_definition_may_come_and_go(): void
    {
        $after = self::record();
        $after['defs']['main']['record']['properties']['subtitle'] = ['type' => 'string', 'maxLength' => 300];
        unset($after['defs']['main']['record']['properties']['link']);
        $after['defs']['extra'] = ['type' => 'token', 'description' => 'new'];

        $this->assertSame([], BreakingChanges::between(self::record(), $after));
    }

    /**
     * @return array<string, array{0: callable(array): array, 1: array<int, string>, 2: string}>
     */
    public static function changes(): array
    {
        $edit = fn (callable $change) => function (array $lexicon) use ($change) {
            $change($lexicon['defs']['main']['record']);

            return $lexicon;
        };

        return [
            'a field changes type' => [$edit(function (&$r) {
                $r['properties']['count'] = ['type' => 'string'];
            }), ['type-change'], Issue::ERROR],
            'a field becomes required' => [$edit(function (&$r) {
                $r['required'][] = 'kind';
            }), ['object-required'], Issue::ERROR],
            'a required field is dropped' => [$edit(function (&$r) {
                $r['required'] = ['createdAt'];
            }), ['object-required'], Issue::ERROR],
            'nullability changes' => [$edit(function (&$r) {
                $r['nullable'] = ['title'];
            }), ['object-nullable'], Issue::ERROR],
            'an enum changes' => [$edit(function (&$r) {
                $r['properties']['kind']['enum'] = ['a', 'b', 'c'];
            }), ['enum-values'], Issue::ERROR],
            'a string format changes' => [$edit(function (&$r) {
                $r['properties']['link']['format'] = 'at-uri';
            }), ['string-format'], Issue::ERROR],
            'a const changes' => [$edit(function (&$r) {
                $r['properties']['flag']['const'] = false;
            }), ['const-value'], Issue::ERROR],
            'a closed union gains a type' => [$edit(function (&$r) {
                $r['properties']['embed']['refs'][] = '#c';
            }), ['union-closed-refs'], Issue::ERROR],
            'a union opens' => [$edit(function (&$r) {
                unset($r['properties']['embed']['closed']);
            }), ['union-open-closed'], Issue::ERROR],
            'an item type changes' => [$edit(function (&$r) {
                $r['properties']['tags']['items'] = ['type' => 'integer'];
            }), ['type-change'], Issue::ERROR],
            'a string limit changes' => [$edit(function (&$r) {
                $r['properties']['title']['maxLength'] = 100;
            }), ['string-length'], Issue::WARN],
            'an integer range changes' => [$edit(function (&$r) {
                $r['properties']['count']['minimum'] = 1;
            }), ['integer-range'], Issue::WARN],
            'an array limit changes' => [$edit(function (&$r) {
                $r['properties']['tags']['maxLength'] = 4;
            }), ['array-length'], Issue::WARN],
            'blob types change' => [$edit(function (&$r) {
                $r['properties']['image']['accept'] = ['image/png'];
            }), ['blob-accept'], Issue::WARN],
            'a blob size changes' => [$edit(function (&$r) {
                $r['properties']['image']['maxSize'] = 5;
            }), ['blob-size'], Issue::WARN],
            'a ref changes' => [$edit(function (&$r) {
                $r['properties']['author']['ref'] = 'com.example.defs#person';
            }), ['ref-change'], Issue::WARN],
        ];
    }

    /**
     * @param  callable(array): array  $change
     * @param  array<int, string>  $names
     */
    #[DataProvider('changes')]
    public function test_each_rule(callable $change, array $names, string $level): void
    {
        $issues = BreakingChanges::between(self::record(), $change(self::record()));

        $this->assertFound($names, $issues);
        $this->assertSame($level, $issues[0]->level);
        $this->assertSame('com.example.thing', $issues[0]->nsid);
    }

    public function test_the_record_key_type_cannot_change(): void
    {
        $after = self::record();
        $after['defs']['main']['key'] = 'any';

        $this->assertFound(['record-key-type'], BreakingChanges::between(self::record(), $after));
    }

    public function test_order_inside_sets_is_not_a_change(): void
    {
        $after = self::record();
        $after['defs']['main']['record']['required'] = ['createdAt', 'title'];
        $after['defs']['main']['record']['properties']['embed']['refs'] = ['#b', '#a'];

        $this->assertSame([], BreakingChanges::between(self::record(), $after));
    }

    public function test_endpoint_parameters_and_bodies_are_compared(): void
    {
        $after = self::query();
        $after['defs']['main']['parameters']['required'] = [];
        $after['defs']['main']['output']['encoding'] = 'application/cbor';
        $after['defs']['main']['output']['schema']['properties']['cursor'] = ['type' => 'integer'];

        $this->assertFound(['params-required', 'body-encoding', 'type-change'], BreakingChanges::between(self::query(), $after));
    }

    public function test_a_definition_changing_kind_is_reported_once(): void
    {
        $after = self::query();
        $after['defs']['main'] = ['type' => 'procedure'];

        $issues = BreakingChanges::between(self::query(), $after);

        $this->assertFound(['type-change'], $issues);
        $this->assertStringContainsString('query to procedure', $issues[0]->message);
    }
}
