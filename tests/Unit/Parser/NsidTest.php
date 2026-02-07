<?php

namespace SocialDept\AtpSchema\Tests\Unit\Parser;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SocialDept\AtpSupport\Nsid;

class NsidTest extends TestCase
{
    public function test_it_parses_valid_nsid(): void
    {
        $nsid = Nsid::parse('app.bsky.feed.post');

        $this->assertInstanceOf(Nsid::class, $nsid);
        $this->assertSame('app.bsky.feed.post', $nsid->toString());
    }

    public function test_it_extracts_authority(): void
    {
        $nsid = Nsid::parse('app.bsky.feed.post');

        $this->assertSame('app.bsky.feed', $nsid->getAuthority());
    }

    public function test_it_extracts_name(): void
    {
        $nsid = Nsid::parse('app.bsky.feed.post');

        $this->assertSame('post', $nsid->getName());
    }

    public function test_it_gets_segments(): void
    {
        $nsid = Nsid::parse('app.bsky.feed.post');

        $this->assertSame(['app', 'bsky', 'feed', 'post'], $nsid->getSegments());
    }

    public function test_it_converts_to_domain(): void
    {
        $nsid = Nsid::parse('app.bsky.feed.post');

        $this->assertSame('post.feed.bsky.app', $nsid->toDomain());
    }

    public function test_it_gets_authority_domain(): void
    {
        $nsid = Nsid::parse('app.bsky.feed.post');

        $this->assertSame('feed.bsky.app', $nsid->getAuthorityDomain());
    }

    public function test_it_converts_to_string(): void
    {
        $nsid = Nsid::parse('app.bsky.feed.post');

        $this->assertSame('app.bsky.feed.post', (string) $nsid);
    }

    public function test_it_validates_nsid_format(): void
    {
        $this->assertTrue(Nsid::isValid('app.bsky.feed.post'));
        $this->assertTrue(Nsid::isValid('com.atproto.repo.getRecord'));
        $this->assertTrue(Nsid::isValid('com.example.my-app.action'));
    }

    public function test_it_rejects_invalid_nsids(): void
    {
        $this->assertFalse(Nsid::isValid(''));
        $this->assertFalse(Nsid::isValid('invalid'));
        $this->assertFalse(Nsid::isValid('no.dots'));
        $this->assertFalse(Nsid::isValid('app.bsky'));
        $this->assertFalse(Nsid::isValid('.invalid.nsid'));
        $this->assertFalse(Nsid::isValid('invalid.nsid.'));
        $this->assertFalse(Nsid::isValid('invalid..nsid.test'));
    }

    public function test_it_throws_on_empty_nsid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NSID cannot be empty');

        Nsid::parse('');
    }

    public function test_it_throws_on_too_few_segments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NSID must have at least 3 segments');

        Nsid::parse('app.bsky');
    }

    public function test_it_throws_on_invalid_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid NSID format');

        Nsid::parse('invalid-nsid');
    }

    public function test_it_checks_equality(): void
    {
        $nsid1 = Nsid::parse('app.bsky.feed.post');
        $nsid2 = Nsid::parse('app.bsky.feed.post');
        $nsid3 = Nsid::parse('app.bsky.feed.like');

        $this->assertTrue($nsid1->equals($nsid2));
        $this->assertFalse($nsid1->equals($nsid3));
    }

    public function test_it_handles_long_nsids(): void
    {
        $nsid = Nsid::parse('com.example.very.long.namespace.with.many.segments.action');

        $this->assertSame('action', $nsid->getName());
        $this->assertSame('com.example.very.long.namespace.with.many.segments', $nsid->getAuthority());
    }

    public function test_it_handles_hyphens(): void
    {
        $nsid = Nsid::parse('com.my-app.feed.get-posts');

        $this->assertSame('get-posts', $nsid->getName());
        $this->assertSame('com.my-app.feed', $nsid->getAuthority());
    }

    public function test_it_rejects_nsid_exceeding_max_length(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NSID exceeds maximum length');

        // Create a string longer than 317 characters
        $longNsid = str_repeat('a.', 160) . 'test';
        Nsid::parse($longNsid);
    }
}
