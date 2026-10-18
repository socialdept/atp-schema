<?php

namespace SocialDept\AtpSchema\Tests\Unit\Support;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SocialDept\AtpSchema\Data\Types\StringType;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Support\Formats;
use SocialDept\AtpSchema\Validation\TypeValidators\StringValidator;

class FormatsTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function uris(): array
    {
        return [
            'https' => ['https://example.com/path?q=1#frag', true],
            'an at-uri with a DID authority' => ['at://did:plc:ikrrjdknlfryzxig7df5vcuw/site.standard.publication/3muag2v5b4fmi', true],
            'a did' => ['did:web:example.com', true],
            'ipfs' => ['ipfs://bafyreib2rxk3rh6kzwq', true],
            'dns' => ['dns:example.com', true],
            'mailto' => ['mailto:alice@example.com', true],
            'no scheme' => ['example.com/path', false],
            'whitespace' => ['https://example.com/a b', false],
            'only a scheme' => ['https:', false],
            'plain words' => ['not a uri', false],
            'too long' => ['https://example.com/'.str_repeat('a', 8192), false],
        ];
    }

    #[DataProvider('uris')]
    public function test_uris(string $value, bool $valid): void
    {
        $this->assertSame($valid, Formats::isUri($value));
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function languages(): array
    {
        return [
            'two letters' => ['en', true],
            'with region' => ['en-US', true],
            'lowercase region' => ['pt-br', true],
            'numeric region' => ['es-419', true],
            'with script and region' => ['zh-Hant-TW', true],
            'three letters' => ['ast', true],
            'grandfathered' => ['i-default', true],
            'one word' => ['invalid', false],
            'empty' => ['', false],
            'capital primary' => ['EN', false],
            'underscore' => ['en_US', false],
        ];
    }

    #[DataProvider('languages')]
    public function test_languages(string $value, bool $valid): void
    {
        $this->assertSame($valid, Formats::isLanguage($value));
    }

    public function test_records_with_an_at_uri_in_a_uri_field_validate(): void
    {
        (new StringType(format: 'uri'))->validate('at://did:plc:abc/site.standard.publication/self', 'site');
        (new StringType(format: 'language'))->validate('es-419', 'langs');

        $this->expectException(RecordValidationException::class);
        (new StringType(format: 'uri'))->validate('no scheme here', 'site');
    }

    public function test_the_string_validator_accepts_an_at_uri_as_a_uri(): void
    {
        $validator = new StringValidator();
        $validator->validate('at://did:plc:abc/app.bsky.feed.post/3abc', ['type' => 'string', 'format' => 'uri'], 'site');

        $this->expectException(RecordValidationException::class);
        $validator->validate('no scheme here', ['type' => 'string', 'format' => 'uri'], 'site');
    }
}
