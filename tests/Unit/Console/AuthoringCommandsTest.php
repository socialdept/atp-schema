<?php

namespace SocialDept\AtpSchema\Tests\Unit\Console;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Authoring\LexiconFiles;
use SocialDept\AtpSchema\Authoring\NetworkLexicons;
use SocialDept\AtpSchema\Authoring\Templates;
use SocialDept\AtpSchema\SchemaServiceProvider;
use SocialDept\AtpSupport\AtpSupportServiceProvider;
use SocialDept\AtpSupport\Facades\Resolver;

class AuthoringCommandsTest extends TestCase
{
    protected const DID = 'did:plc:authority';

    protected string $dir;

    /**
     * TXT names that resolve, by name.
     *
     * @var array<string, string>
     */
    protected array $dns = [];

    protected function getPackageProviders($app): array
    {
        return [AtpSupportServiceProvider::class, SchemaServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir().'/atp-schema-authoring-'.bin2hex(random_bytes(4));
        mkdir($this->dir);
        config()->set('atp-schema.lexicon_path', $this->dir.'/lexicons');
        config()->set('atp-schema.lock_file', $this->dir.'/lexicons.lock.json');

        $this->app->instance(NetworkLexicons::class, new NetworkLexicons(fn (string $name) => $this->dns[$name] ?? null));
        Resolver::shouldReceive('resolvePds')->with(self::DID)->andReturn('https://pds.example');
    }

    protected function tearDown(): void
    {
        exec('rm -rf '.escapeshellarg($this->dir));

        parent::tearDown();
    }

    /**
     * Publish lexicons at the fake authority's PDS.
     *
     * @param  array<string, array<string, mixed>>  $lexicons
     */
    protected function publish(array $lexicons): void
    {
        $this->dns['_lexicon.example.com'] = self::DID;

        Http::fake(function ($request) use ($lexicons) {
            $nsid = $request['rkey'] ?? null;

            return isset($lexicons[$nsid])
                ? Http::response(['uri' => 'at://'.self::DID.'/com.atproto.lexicon.schema/'.$nsid, 'cid' => 'bafy-'.$nsid, 'value' => ['$type' => 'com.atproto.lexicon.schema', ...$lexicons[$nsid]]])
                : Http::response(['error' => 'RecordNotFound', 'message' => 'Could not locate record'], 400);
        });
    }

    protected function files(): LexiconFiles
    {
        return LexiconFiles::fromConfig();
    }

    public function test_new_writes_a_template_at_the_nsid_path_and_keeps_existing_files(): void
    {
        $this->artisan('schema:new', ['type' => 'record', 'nsid' => 'com.example.thing'])->assertSuccessful();

        $this->assertFileExists($this->dir.'/lexicons/com/example/thing.json');
        $this->assertSame('record', $this->files()->read('com.example.thing')['defs']['main']['type']);

        $this->artisan('schema:new', ['type' => 'query', 'nsid' => 'com.example.thing'])->assertFailed();
        $this->artisan('schema:new', ['type' => 'query', 'nsid' => 'com.example.thing', '--force' => true])->assertSuccessful();
        $this->artisan('schema:new', ['type' => 'gadget', 'nsid' => 'com.example.other'])->assertFailed();
        $this->artisan('schema:new', ['type' => 'record', 'nsid' => 'not an nsid'])->assertFailed();
    }

    public function test_lint_passes_clean_files_and_fails_on_errors(): void
    {
        $this->files()->write('com.example.thing', Templates::make('record', 'com.example.thing'));

        $this->artisan('schema:lint')->expectsOutputToContain('com.example.thing')->assertSuccessful();

        $broken = Templates::make('record', 'com.example.broken');
        $broken['defs']['other'] = $broken['defs']['main'];
        $this->files()->write('com.example.broken', $broken);

        $this->artisan('schema:lint')->expectsOutputToContain('non-main-primary')->assertFailed();
        $this->artisan('schema:lint', ['nsid' => ['com.example.thing']])->assertSuccessful();
        $this->artisan('schema:lint', ['nsid' => ['com.example.broken'], '--json' => true])
            ->expectsOutputToContain('"name":"non-main-primary"')
            ->assertFailed();
    }

    public function test_breaking_compares_with_the_published_version(): void
    {
        $published = Templates::make('record', 'com.example.thing');
        $this->publish(['com.example.thing' => $published]);

        $local = $published;
        $local['defs']['main']['record']['properties']['note'] = ['type' => 'string', 'maxLength' => 100];
        $this->files()->write('com.example.thing', $local);
        $this->files()->write('com.example.unpublished', Templates::make('record', 'com.example.unpublished'));

        $this->artisan('schema:breaking')
            ->expectsOutputToContain('not published')
            ->assertSuccessful();

        $local['defs']['main']['record']['required'][] = 'note';
        $this->files()->write('com.example.thing', $local);

        $this->artisan('schema:breaking', ['nsid' => ['com.example.thing']])
            ->expectsOutputToContain('object-required')
            ->assertFailed();
    }

    public function test_breaking_can_compare_with_a_file(): void
    {
        $before = Templates::make('record', 'com.example.thing');
        $after = $before;
        $after['defs']['main']['key'] = 'any';
        $this->files()->write('com.example.thing', $after);
        file_put_contents($this->dir.'/before.json', json_encode($before));

        $this->artisan('schema:breaking', ['nsid' => ['com.example.thing'], '--against' => $this->dir.'/before.json'])
            ->expectsOutputToContain('record-key-type')
            ->assertFailed();
    }

    public function test_diff_lists_changes_against_the_published_version(): void
    {
        $published = Templates::make('record', 'com.example.thing');
        $this->publish(['com.example.thing' => $published]);

        $local = $published;
        $local['defs']['main']['description'] = 'Changed.';
        $this->files()->write('com.example.thing', $local);

        $this->artisan('schema:diff', ['nsid' => 'com.example.thing'])
            ->expectsOutputToContain('defs.main.description')
            ->assertSuccessful();

        $this->files()->write('com.example.thing', $published);

        $this->artisan('schema:diff', ['nsid' => 'com.example.thing'])
            ->expectsOutputToContain('matches')
            ->assertSuccessful();
    }

    public function test_pull_copies_a_published_lexicon_and_locks_it(): void
    {
        $published = Templates::make('query', 'com.example.getThing');
        $this->publish(['com.example.getThing' => $published]);

        $this->artisan('schema:pull', ['nsid' => ['com.example.getThing']])
            ->expectsOutputToContain('pulled')
            ->assertSuccessful();

        $this->assertEquals($published, $this->files()->read('com.example.getThing'));
        $lock = json_decode(file_get_contents($this->dir.'/lexicons.lock.json'), true);
        $this->assertSame([
            'did' => self::DID,
            'uri' => 'at://did:plc:authority/com.atproto.lexicon.schema/com.example.getThing',
            'cid' => 'bafy-com.example.getThing',
        ], $lock['lexicons']['com.example.getThing']);

        // With no names, it pulls everything in the lock file again.
        $this->artisan('schema:pull')->expectsOutputToContain('up to date')->assertSuccessful();
    }

    public function test_pull_keeps_a_local_copy_that_differs_unless_forced(): void
    {
        $published = Templates::make('query', 'com.example.getThing');
        $this->publish(['com.example.getThing' => $published]);

        $local = $published;
        $local['defs']['main']['description'] = 'Edited here.';
        $this->files()->write('com.example.getThing', $local);

        $this->artisan('schema:pull', ['nsid' => ['com.example.getThing']])->expectsOutputToContain('local copy differs')->assertSuccessful();
        $this->assertSame('Edited here.', $this->files()->read('com.example.getThing')['defs']['main']['description']);

        $this->artisan('schema:pull', ['nsid' => ['com.example.getThing'], '--force' => true])->expectsOutputToContain('updated')->assertSuccessful();
        $this->assertEquals($published, $this->files()->read('com.example.getThing'));
    }

    public function test_pull_fails_for_a_lexicon_nobody_publishes(): void
    {
        $this->publish([]);

        $this->artisan('schema:pull', ['nsid' => ['com.example.missing']])->expectsOutputToContain('not published')->assertFailed();
        $this->artisan('schema:pull', ['nsid' => ['org.nobody.thing']])->expectsOutputToContain('_lexicon.nobody.org')->assertFailed();
    }

    public function test_check_dns_prints_the_records_that_are_missing(): void
    {
        $this->files()->write('com.example.thing', Templates::make('record', 'com.example.thing'));
        $this->files()->write('com.example.feed.post', Templates::make('record', 'com.example.feed.post'));
        $this->dns['_lexicon.example.com'] = self::DID;

        // Each authority needs its own record: com.example.feed is not
        // covered by com.example's.
        $this->artisan('schema:check-dns', ['--did' => self::DID])
            ->expectsOutputToContain('_lexicon.feed.example.com  TXT  "did='.self::DID.'"')
            ->assertFailed();

        $this->dns['_lexicon.feed.example.com'] = self::DID;
        $this->artisan('schema:check-dns', ['--did' => self::DID])->assertSuccessful();
        $this->artisan('schema:check-dns', ['--did' => 'did:plc:someone-else'])->expectsOutputToContain('not did:plc:someone-else')->assertFailed();
    }
}
