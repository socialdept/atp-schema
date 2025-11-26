<?php

namespace SocialDept\AtpSchema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Generator\NamingConverter;

class NamingConverterTest extends TestCase
{
    protected NamingConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = new NamingConverter('App\\Lexicon');
    }

    public function test_it_converts_nsid_to_class_name(): void
    {
        $className = $this->converter->nsidToClassName('app.bsky.feed.post');

        $this->assertSame('App\\Lexicon\\App\\Bsky\\Feed\\Post', $className);
    }

    public function test_it_converts_nsid_to_namespace(): void
    {
        $namespace = $this->converter->nsidToNamespace('app.bsky.feed.post');

        $this->assertSame('App\\Lexicon\\App\\Bsky\\Feed', $namespace);
    }

    public function test_it_handles_multi_part_names(): void
    {
        $className = $this->converter->toClassName('feed.post');

        $this->assertSame('FeedPost', $className);
    }

    public function test_it_handles_single_part_names(): void
    {
        $className = $this->converter->toClassName('post');

        $this->assertSame('Post', $className);
    }

    public function test_it_converts_to_pascal_case(): void
    {
        $this->assertSame('HelloWorld', $this->converter->toPascalCase('hello_world'));
        $this->assertSame('HelloWorld', $this->converter->toPascalCase('hello-world'));
        $this->assertSame('HelloWorld', $this->converter->toPascalCase('hello world'));
        $this->assertSame('HelloWorld', $this->converter->toPascalCase('helloWorld'));
        $this->assertSame('Foo', $this->converter->toPascalCase('foo'));
    }

    public function test_it_converts_to_camel_case(): void
    {
        $this->assertSame('helloWorld', $this->converter->toCamelCase('hello_world'));
        $this->assertSame('helloWorld', $this->converter->toCamelCase('hello-world'));
        $this->assertSame('helloWorld', $this->converter->toCamelCase('hello world'));
        $this->assertSame('helloWorld', $this->converter->toCamelCase('HelloWorld'));
        $this->assertSame('foo', $this->converter->toCamelCase('foo'));
    }

    public function test_it_converts_to_snake_case(): void
    {
        $this->assertSame('hello_world', $this->converter->toSnakeCase('HelloWorld'));
        $this->assertSame('hello_world', $this->converter->toSnakeCase('helloWorld'));
        $this->assertSame('foo', $this->converter->toSnakeCase('foo'));
        $this->assertSame('foo_bar_baz', $this->converter->toSnakeCase('FooBarBaz'));
    }

    public function test_it_converts_to_kebab_case(): void
    {
        $this->assertSame('hello-world', $this->converter->toKebabCase('HelloWorld'));
        $this->assertSame('hello-world', $this->converter->toKebabCase('helloWorld'));
        $this->assertSame('foo', $this->converter->toKebabCase('foo'));
        $this->assertSame('foo-bar-baz', $this->converter->toKebabCase('FooBarBaz'));
    }

    public function test_it_pluralizes_words(): void
    {
        $this->assertSame('posts', $this->converter->pluralize('post'));
        $this->assertSame('categories', $this->converter->pluralize('category'));
        $this->assertSame('boxes', $this->converter->pluralize('box'));
        $this->assertSame('churches', $this->converter->pluralize('church'));
        $this->assertSame('bushes', $this->converter->pluralize('bush'));
        $this->assertSame('postses', $this->converter->pluralize('posts')); // 'posts' ends with 's', gets 'es'
    }

    public function test_it_singularizes_words(): void
    {
        $this->assertSame('post', $this->converter->singularize('posts'));
        $this->assertSame('category', $this->converter->singularize('categories'));
        $this->assertSame('box', $this->converter->singularize('boxes'));
        $this->assertSame('church', $this->converter->singularize('churches'));
        $this->assertSame('bush', $this->converter->singularize('bushes'));
        $this->assertSame('post', $this->converter->singularize('post')); // Already singular
    }

    public function test_it_handles_complex_nsid(): void
    {
        $className = $this->converter->nsidToClassName('com.atproto.repo.getRecord');

        $this->assertSame('App\\Lexicon\\Com\\Atproto\\Repo\\GetRecord', $className);
    }

    public function test_it_gets_base_namespace(): void
    {
        $namespace = $this->converter->getBaseNamespace();

        $this->assertSame('App\\Lexicon', $namespace);
    }

    public function test_it_sets_base_namespace(): void
    {
        $this->converter->setBaseNamespace('Custom\\Namespace');

        $this->assertSame('Custom\\Namespace', $this->converter->getBaseNamespace());
    }

    public function test_it_strips_trailing_slash_from_namespace(): void
    {
        $converter = new NamingConverter('App\\Lexicon\\');

        $this->assertSame('App\\Lexicon', $converter->getBaseNamespace());
    }

    public function test_it_handles_three_part_authority(): void
    {
        $className = $this->converter->nsidToClassName('com.example.api.getUser');

        $this->assertSame('App\\Lexicon\\Com\\Example\\Api\\GetUser', $className);
    }

    public function test_it_handles_hyphens_in_names(): void
    {
        $className = $this->converter->toClassName('my-post');

        $this->assertSame('MyPost', $className);
    }

    public function test_it_handles_underscores_in_names(): void
    {
        $className = $this->converter->toClassName('my_post');

        $this->assertSame('MyPost', $className);
    }

    public function test_namespace_parts_are_reversed(): void
    {
        // app.bsky.feed should become App\Bsky\Feed (authority-first)
        $namespace = $this->converter->nsidToNamespace('app.bsky.feed.post');

        $this->assertStringContainsString('App\\Bsky\\Feed', $namespace);
    }

    public function test_it_handles_single_letter_parts(): void
    {
        $pascalCase = $this->converter->toPascalCase('a');

        $this->assertSame('A', $pascalCase);
    }

    public function test_it_handles_numbers_in_names(): void
    {
        $className = $this->converter->toClassName('post2');

        $this->assertSame('Post2', $className);
    }
}
