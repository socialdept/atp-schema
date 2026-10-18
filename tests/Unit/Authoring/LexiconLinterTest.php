<?php

namespace SocialDept\AtpSchema\Tests\Unit\Authoring;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SocialDept\AtpSchema\Authoring\Issue;
use SocialDept\AtpSchema\Authoring\LexiconLinter;
use SocialDept\AtpSchema\Authoring\Templates;

class LexiconLinterTest extends TestCase
{
    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    protected static function record(array $properties = [], array $extraDefs = []): array
    {
        return [
            'lexicon' => 1,
            'id' => 'example.lexicon.record',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'description' => 'minimal description',
                    'key' => 'any',
                    'record' => ['type' => 'object', 'properties' => $properties],
                ],
                ...$extraDefs,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $lexicon
     * @return array<int, string>
     */
    protected function names(array $lexicon): array
    {
        $names = array_map(fn (Issue $issue) => $issue->name, (new LexiconLinter())->lint($lexicon));
        sort($names);

        return $names;
    }

    /**
     * The four fixtures from Bluesky's reference linter (indigo's
     * lex/lexlint/testdata/lint-examples.json), so both linters agree on
     * them.
     *
     * @return array<string, array{0: array<string, mixed>, 1: array<int, string>}>
     */
    public static function referenceExamples(): array
    {
        return [
            'minimal, no lint problems' => [self::record(), []],
            'bad field name' => [self::record(['one.two' => ['type' => 'string', 'maxLength' => 20]]), ['field-name-syntax']],
            'reserved keyword field name' => [self::record(['id' => ['type' => 'string', 'maxLength' => 20]]), ['field-name-syntax']],
            'local ref in string known values' => [
                self::record(['strField' => ['type' => 'string', 'maxLength' => 100, 'knownValues' => ['#green']]], ['green' => ['type' => 'token', 'description' => 'asdf']]),
                ['known-string-local-ref'],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $lexicon
     * @param  array<int, string>  $expected
     */
    #[DataProvider('referenceExamples')]
    public function test_the_reference_examples(array $lexicon, array $expected): void
    {
        $this->assertSame($expected, $this->names($lexicon));
    }

    public function test_every_template_is_clean(): void
    {
        foreach (Templates::TYPES as $type) {
            $issues = (new LexiconLinter())->lint(json_decode(json_encode(Templates::make($type, 'com.example.thing')), true));

            $this->assertSame([], array_map(fn (Issue $issue) => $issue->name.': '.$issue->message, $issues), $type);
        }
    }

    public function test_structure_and_version_errors(): void
    {
        $this->assertSame(['invalid-nsid', 'lexicon-version'], $this->names(['lexicon' => 2, 'id' => 'nope']));
        $this->assertContains('lexicon-schema', $this->names(['lexicon' => 1, 'id' => 'com.example.thing', 'defs' => ['main' => ['type' => 'nonsense']]]));
    }

    public function test_primary_definitions_must_be_main_and_described(): void
    {
        $lexicon = self::record();
        $lexicon['defs']['other'] = $lexicon['defs']['main'];
        unset($lexicon['defs']['main']['description']);

        $this->assertSame(['missing-primary-description', 'non-main-primary'], $this->names($lexicon));
    }

    public function test_endpoints_should_define_output_and_encoding(): void
    {
        $lexicon = [
            'lexicon' => 1,
            'id' => 'com.example.doThing',
            'defs' => ['main' => [
                'type' => 'procedure',
                'description' => 'Does a thing.',
                'input' => ['schema' => ['type' => 'object', 'properties' => []]],
            ]],
        ];

        $this->assertSame(['endpoint-output-undefined', 'unspecified-encoding'], $this->names($lexicon));
    }

    public function test_field_limits_and_shapes(): void
    {
        $lexicon = self::record(
            [
                'body' => ['type' => 'string', 'maxLength' => 100000],
                'note' => ['type' => 'string'],
                'data' => ['type' => 'bytes'],
                'blobby' => ['type' => 'bytes', 'maxLength' => 100000],
            ],
            ['flavor' => ['type' => 'token']],
        );
        $lexicon['defs']['main']['record']['nullable'] = ['note'];

        $this->assertSame(['large-bytes', 'large-string', 'nullable-and-optional', 'undescribed-token', 'unlimited-bytes', 'unlimited-string'], $this->names($lexicon));
    }

    public function test_a_defs_file_should_not_have_main(): void
    {
        $lexicon = ['lexicon' => 1, 'id' => 'com.example.defs', 'defs' => ['main' => ['type' => 'token', 'description' => 'x']]];

        $this->assertSame(['defs-main-definition'], $this->names($lexicon));
    }
}
