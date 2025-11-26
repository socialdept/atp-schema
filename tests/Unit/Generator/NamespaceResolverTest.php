<?php

namespace SocialDept\AtpSchema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Generator\NamespaceResolver;

class NamespaceResolverTest extends TestCase
{
    protected NamespaceResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new NamespaceResolver('App\\Lexicon');
    }

    public function test_it_resolves_namespace_from_nsid(): void
    {
        $namespace = $this->resolver->resolveNamespace('app.bsky.feed.post');

        $this->assertSame('App\\Lexicon\\App\\Bsky\\Feed', $namespace);
    }

    public function test_it_resolves_class_name_from_nsid(): void
    {
        $className = $this->resolver->resolveClassName('app.bsky.feed.post');

        $this->assertSame('Post', $className);
    }

    public function test_it_resolves_class_name_with_definition(): void
    {
        $className = $this->resolver->resolveClassName('app.bsky.feed.post', 'replyRef');

        $this->assertSame('ReplyRef', $className);
    }

    public function test_it_resolves_fully_qualified_name(): void
    {
        $fqn = $this->resolver->resolveFullyQualifiedName('app.bsky.feed.post');

        $this->assertSame('App\\Lexicon\\App\\Bsky\\Feed\\Post', $fqn);
    }

    public function test_it_resolves_fully_qualified_name_with_definition(): void
    {
        $fqn = $this->resolver->resolveFullyQualifiedName('app.bsky.feed.post', 'replyRef');

        $this->assertSame('App\\Lexicon\\App\\Bsky\\Feed\\ReplyRef', $fqn);
    }

    public function test_it_resolves_file_path(): void
    {
        $path = $this->resolver->resolveFilePath('app.bsky.feed.post', '/var/www/app');

        $this->assertSame('/var/www/app/App/Bsky/Feed/Post.php', $path);
    }

    public function test_it_handles_hyphens_in_nsid(): void
    {
        $className = $this->resolver->resolveClassName('app.bsky.feed.feed-view');

        $this->assertSame('FeedView', $className);
    }

    public function test_it_gets_base_namespace(): void
    {
        $this->assertSame('App\\Lexicon', $this->resolver->getBaseNamespace());
    }

    public function test_it_handles_custom_base_namespace(): void
    {
        $resolver = new NamespaceResolver('Custom\\Namespace');

        $namespace = $resolver->resolveNamespace('app.bsky.feed.post');

        $this->assertSame('Custom\\Namespace\\App\\Bsky\\Feed', $namespace);
    }
}
