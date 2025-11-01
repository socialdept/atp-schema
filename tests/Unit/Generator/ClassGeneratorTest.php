<?php

namespace SocialDept\Schema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Exceptions\GenerationException;
use SocialDept\Schema\Generator\ClassGenerator;
use SocialDept\Schema\Parser\Nsid;

class ClassGeneratorTest extends TestCase
{
    protected ClassGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new ClassGenerator;
    }

    public function test_it_generates_simple_record_class(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'text' => ['type' => 'string'],
                'createdAt' => ['type' => 'string'],
            ],
            'required' => ['text', 'createdAt'],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('namespace App\\Lexicon\\Test\\App;', $code);
        $this->assertStringContainsString('class Post extends \\SocialDept\\Schema\\Data\\Data', $code);
        $this->assertStringContainsString('public readonly string $text;', $code);
        $this->assertStringContainsString('public readonly string $createdAt;', $code);
        $this->assertStringContainsString('public static function getLexicon(): string', $code);
        $this->assertStringContainsString("return 'app.test.post';", $code);
    }

    public function test_it_handles_optional_properties(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'title' => ['type' => 'string'],
                'subtitle' => ['type' => 'string'],
            ],
            'required' => ['title'],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('public readonly string $title', $code);
        $this->assertStringContainsString('public readonly ?string $subtitle', $code);
    }

    public function test_it_generates_constructor_with_parameters(): void
    {
        $document = $this->createDocument('app.test.user', [
            'type' => 'record',
            'properties' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
            ],
            'required' => ['name'],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('public function __construct(', $code);
        $this->assertStringContainsString('public readonly string $name', $code);
        $this->assertStringContainsString('public readonly ?int $age = null', $code);
    }

    public function test_it_generates_from_array_method(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'text' => ['type' => 'string'],
            ],
            'required' => ['text'],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('public static function fromArray(array $data): static', $code);
        $this->assertStringContainsString('return new static(', $code);
        $this->assertStringContainsString("text: \$data['text'] ?? null", $code);
    }

    public function test_it_includes_use_statements(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'text' => ['type' => 'string'],
            ],
            'required' => ['text'],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('use SocialDept\\Schema\\Data\\Data;', $code);
    }

    public function test_it_includes_ref_use_statements(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'author' => [
                    'type' => 'ref',
                    'ref' => 'app.test.author',
                ],
            ],
            'required' => ['author'],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('use App\\Lexicon\\Test\\App\\Author;', $code);
    }

    public function test_it_includes_blob_use_statements(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'image' => ['type' => 'blob'],
            ],
            'required' => [],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('use SocialDept\\Schema\\Data\\BlobReference;', $code);
    }

    public function test_it_generates_class_docblock(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'description' => 'A social media post',
            'properties' => ['text' => ['type' => 'string']],
            'required' => ['text'],
        ], 'A social media post');

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('/**', $code);
        $this->assertStringContainsString('* A social media post', $code);
        $this->assertStringContainsString('* Lexicon: app.test.post', $code);
        $this->assertStringContainsString('* Type: record', $code);
    }

    public function test_it_generates_property_docblocks(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'text' => [
                    'type' => 'string',
                    'description' => 'The post content',
                ],
            ],
            'required' => ['text'],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('* The post content', $code);
        $this->assertStringContainsString('* @var string', $code);
    }

    public function test_it_throws_when_no_main_definition(): void
    {
        $document = new LexiconDocument(
            lexicon: 1,
            id: Nsid::parse('app.test.empty'),
            defs: [],
            description: null,
            source: null,
            raw: []
        );

        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('No main definition found');

        $this->generator->generate($document);
    }

    public function test_it_throws_for_non_record_types(): void
    {
        $document = $this->createDocument('app.test.query', [
            'type' => 'query',
        ]);

        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('Can only generate classes for record and object types');

        $this->generator->generate($document);
    }

    public function test_it_handles_empty_properties(): void
    {
        $document = $this->createDocument('app.test.empty', [
            'type' => 'record',
            'properties' => [],
            'required' => [],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('class Empty', $code);
        $this->assertStringContainsString('public static function fromArray(array $data): static', $code);
        $this->assertStringContainsString('return new static();', $code);
    }

    public function test_it_handles_array_of_refs(): void
    {
        $document = $this->createDocument('app.test.feed', [
            'type' => 'record',
            'properties' => [
                'posts' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'ref',
                        'ref' => 'app.test.post',
                    ],
                ],
            ],
            'required' => ['posts'],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('use App\\Lexicon\\Test\\App\\Post;', $code);
        $this->assertStringContainsString('public readonly array $posts', $code);
        $this->assertStringContainsString('array_map(fn ($item) => Post::fromArray($item)', $code);
    }

    public function test_it_generates_object_type(): void
    {
        $document = $this->createDocument('app.test.config', [
            'type' => 'object',
            'properties' => [
                'enabled' => ['type' => 'boolean'],
            ],
            'required' => ['enabled'],
        ]);

        $code = $this->generator->generate($document);

        $this->assertStringContainsString('class Config', $code);
        $this->assertStringContainsString('public readonly bool $enabled', $code);
    }

    public function test_it_sorts_use_statements(): void
    {
        $document = $this->createDocument('app.test.complex', [
            'type' => 'record',
            'properties' => [
                'image' => ['type' => 'blob'],
                'author' => [
                    'type' => 'ref',
                    'ref' => 'app.test.author',
                ],
            ],
            'required' => [],
        ]);

        $code = $this->generator->generate($document);

        // Use statements should be sorted
        $dataPos = strpos($code, 'use App\\Lexicon\\Test\\App\\Author;');
        $blobPos = strpos($code, 'use SocialDept\\Schema\\Data\\BlobReference;');
        $basePos = strpos($code, 'use SocialDept\\Schema\\Data\\Data;');

        $this->assertNotFalse($dataPos);
        $this->assertNotFalse($blobPos);
        $this->assertNotFalse($basePos);
        $this->assertLessThan($blobPos, $dataPos); // App before SocialDept
    }

    public function test_it_provides_accessor_methods(): void
    {
        $naming = $this->generator->getNaming();
        $typeMapper = $this->generator->getTypeMapper();
        $renderer = $this->generator->getRenderer();

        $this->assertInstanceOf(\SocialDept\Schema\Generator\NamingConverter::class, $naming);
        $this->assertInstanceOf(\SocialDept\Schema\Generator\TypeMapper::class, $typeMapper);
        $this->assertInstanceOf(\SocialDept\Schema\Generator\StubRenderer::class, $renderer);
    }

    /**
     * Helper to create a test document.
     *
     * @param  array<string, mixed>  $mainDef
     */
    protected function createDocument(string $nsid, array $mainDef, ?string $description = null): LexiconDocument
    {
        return new LexiconDocument(
            lexicon: 1,
            id: Nsid::parse($nsid),
            defs: ['main' => $mainDef],
            description: $description,
            source: null,
            raw: [
                'lexicon' => 1,
                'id' => $nsid,
                'defs' => ['main' => $mainDef],
            ]
        );
    }
}
