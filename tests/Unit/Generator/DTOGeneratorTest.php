<?php

namespace SocialDept\AtpSchema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Generator\DTOGenerator;
use SocialDept\AtpSchema\Parser\SchemaLoader;

class DTOGeneratorTest extends TestCase
{
    protected DTOGenerator $generator;

    protected string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $fixturesPath = __DIR__.'/../../fixtures';
        $loader = new SchemaLoader([$fixturesPath], false);

        $this->tempDir = sys_get_temp_dir().'/schema-gen-test-'.uniqid();

        $this->generator = new DTOGenerator(
            schemaLoader: $loader,
            baseNamespace: 'Test\\Generated',
            outputDirectory: $this->tempDir
        );
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $this->deleteDirectory($this->tempDir);
        }

        parent::tearDown();
    }

    protected function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir.'/'.$item;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    public function test_it_generates_from_nsid(): void
    {
        $files = $this->generator->generateByNsid('app.bsky.feed.post', ['dryRun' => true]);

        $this->assertNotEmpty($files);
        $this->assertIsArray($files);
    }

    public function test_it_generates_code_from_document(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'test.example.record',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'record' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);

        $code = $this->generator->generate($document);

        $this->assertIsString($code);
        $this->assertStringContainsString('class Record', $code);
    }

    public function test_it_previews_generated_code(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'test.example.record',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'record' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);

        $code = $this->generator->preview($document);

        $this->assertIsString($code);
        $this->assertStringContainsString('<?php', $code);
    }

    public function test_it_gets_metadata(): void
    {
        $metadata = $this->generator->getMetadata('app.bsky.feed.post');

        $this->assertArrayHasKey('nsid', $metadata);
        $this->assertArrayHasKey('namespace', $metadata);
        $this->assertArrayHasKey('className', $metadata);
        $this->assertArrayHasKey('fullyQualifiedName', $metadata);
        $this->assertArrayHasKey('type', $metadata);

        $this->assertSame('app.bsky.feed.post', $metadata['nsid']);
        $this->assertSame('Test\\Generated\\App\\Bsky\\Feed', $metadata['namespace']);
        $this->assertSame('Post', $metadata['className']);
    }

    public function test_it_validates_generated_code(): void
    {
        $validCode = '<?php class Test {}';

        $this->assertTrue($this->generator->validate($validCode));
    }

    public function test_it_sets_options(): void
    {
        $this->generator->setOptions([
            'baseNamespace' => 'Custom\\Namespace',
            'outputDirectory' => '/custom/path',
        ]);

        $metadata = $this->generator->getMetadata('app.bsky.feed.post');

        $this->assertStringStartsWith('Custom\\Namespace', $metadata['namespace']);
    }
}
