<?php

namespace SocialDept\AtpSchema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Generator\DocBlockGenerator;
use SocialDept\AtpSchema\Parser\Nsid;

class DocBlockGeneratorTest extends TestCase
{
    protected DocBlockGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new DocBlockGenerator();
    }

    public function test_it_generates_class_docblock_with_description(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'description' => 'A social media post',
            'properties' => [],
        ], 'A social media post');

        $docBlock = $this->generator->generateClassDocBlock($document, $document->getMainDefinition());

        $this->assertStringContainsString('/**', $docBlock);
        $this->assertStringContainsString('* A social media post', $docBlock);
        $this->assertStringContainsString('* Lexicon: app.test.post', $docBlock);
        $this->assertStringContainsString('* Type: record', $docBlock);
    }

    public function test_it_generates_class_docblock_with_property_tags(): void
    {
        $document = $this->createDocument('app.test.user', [
            'type' => 'record',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'description' => 'User name',
                ],
                'age' => [
                    'type' => 'integer',
                ],
            ],
            'required' => ['name'],
        ]);

        $docBlock = $this->generator->generateClassDocBlock($document, $document->getMainDefinition());

        $this->assertStringContainsString('@property string $name User name', $docBlock);
        $this->assertStringContainsString('@property int|null $age', $docBlock);
    }

    public function test_it_includes_validation_constraints_in_class_docblock(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'text' => [
                    'type' => 'string',
                    'maxLength' => 280,
                ],
            ],
            'required' => ['text'],
        ]);

        $docBlock = $this->generator->generateClassDocBlock($document, $document->getMainDefinition());

        $this->assertStringContainsString('Constraints:', $docBlock);
        $this->assertStringContainsString('Required: text', $docBlock);
        $this->assertStringContainsString('text: Max length: 280', $docBlock);
    }

    public function test_it_generates_property_docblock(): void
    {
        $docBlock = $this->generator->generatePropertyDocBlock(
            'title',
            [
                'type' => 'string',
                'description' => 'The post title',
            ],
            true
        );

        $this->assertStringContainsString('/**', $docBlock);
        $this->assertStringContainsString('* The post title', $docBlock);
        $this->assertStringContainsString('* @var string', $docBlock);
        $this->assertStringContainsString('*/', $docBlock);
    }

    public function test_it_includes_constraints_in_property_docblock(): void
    {
        $docBlock = $this->generator->generatePropertyDocBlock(
            'text',
            [
                'type' => 'string',
                'maxLength' => 280,
                'minLength' => 1,
            ],
            true
        );

        $this->assertStringContainsString('@constraint Max length: 280', $docBlock);
        $this->assertStringContainsString('@constraint Min length: 1', $docBlock);
    }

    public function test_it_generates_method_docblock(): void
    {
        $docBlock = $this->generator->generateMethodDocBlock(
            'Create a new post',
            'static',
            [
                ['name' => 'text', 'type' => 'string', 'description' => 'Post text'],
                ['name' => 'author', 'type' => 'string'],
            ]
        );

        $this->assertStringContainsString('* Create a new post', $docBlock);
        $this->assertStringContainsString('* @param  string  $text  Post text', $docBlock);
        $this->assertStringContainsString('* @param  string  $author', $docBlock);
        $this->assertStringContainsString('* @return static', $docBlock);
    }

    public function test_it_handles_void_return_type(): void
    {
        $docBlock = $this->generator->generateMethodDocBlock(
            'Process data',
            'void',
            []
        );

        $this->assertStringNotContainsString('@return', $docBlock);
    }

    public function test_it_includes_throws_annotation(): void
    {
        $docBlock = $this->generator->generateMethodDocBlock(
            'Validate data',
            'bool',
            [],
            '\\InvalidArgumentException'
        );

        $this->assertStringContainsString('@throws \\InvalidArgumentException', $docBlock);
    }

    public function test_it_wraps_long_descriptions(): void
    {
        $longDescription = 'This is a very long description that should be wrapped across multiple lines when it exceeds the maximum line width of eighty characters including the comment prefix and should definitely span more than one line';

        $docBlock = $this->generator->generatePropertyDocBlock(
            'description',
            [
                'type' => 'string',
                'description' => $longDescription,
            ],
            true
        );

        // Just verify the long description is present in the docblock
        $this->assertStringContainsString('This is a very long description', $docBlock);

        // And that it doesn't exceed reasonable line lengths
        $lines = explode("\n", $docBlock);
        foreach ($lines as $line) {
            $this->assertLessThan(120, strlen($line), 'Line too long: '.$line);
        }
    }

    public function test_it_extracts_max_length_constraint(): void
    {
        $constraints = $this->invokeMethod('extractPropertyConstraints', [
            ['maxLength' => 100],
        ]);

        $this->assertContains('@constraint Max length: 100', $constraints);
    }

    public function test_it_extracts_min_length_constraint(): void
    {
        $constraints = $this->invokeMethod('extractPropertyConstraints', [
            ['minLength' => 5],
        ]);

        $this->assertContains('@constraint Min length: 5', $constraints);
    }

    public function test_it_extracts_grapheme_constraints(): void
    {
        $constraints = $this->invokeMethod('extractPropertyConstraints', [
            ['maxGraphemes' => 280, 'minGraphemes' => 1],
        ]);

        $this->assertContains('@constraint Max graphemes: 280', $constraints);
        $this->assertContains('@constraint Min graphemes: 1', $constraints);
    }

    public function test_it_extracts_number_constraints(): void
    {
        $constraints = $this->invokeMethod('extractPropertyConstraints', [
            ['maximum' => 100, 'minimum' => 0],
        ]);

        $this->assertContains('@constraint Maximum: 100', $constraints);
        $this->assertContains('@constraint Minimum: 0', $constraints);
    }

    public function test_it_extracts_array_constraints(): void
    {
        $constraints = $this->invokeMethod('extractPropertyConstraints', [
            ['maxItems' => 10, 'minItems' => 1],
        ]);

        $this->assertContains('@constraint Max items: 10', $constraints);
        $this->assertContains('@constraint Min items: 1', $constraints);
    }

    public function test_it_extracts_enum_constraint(): void
    {
        $constraints = $this->invokeMethod('extractPropertyConstraints', [
            ['enum' => ['active', 'inactive', 'pending']],
        ]);

        $this->assertContains('@constraint Enum: active, inactive, pending', $constraints);
    }

    public function test_it_extracts_format_constraint(): void
    {
        $constraints = $this->invokeMethod('extractPropertyConstraints', [
            ['format' => 'datetime'],
        ]);

        $this->assertContains('@constraint Format: datetime', $constraints);
    }

    public function test_it_extracts_const_constraint(): void
    {
        $constraints = $this->invokeMethod('extractPropertyConstraints', [
            ['const' => true],
        ]);

        $this->assertContains('@constraint Const: true', $constraints);
    }

    public function test_it_generates_simple_docblock(): void
    {
        $docBlock = $this->generator->generateSimple('A simple description');

        $this->assertSame("    /**\n     * A simple description\n     */", $docBlock);
    }

    public function test_it_generates_one_line_docblock(): void
    {
        $docBlock = $this->generator->generateOneLine('Quick note');

        $this->assertSame('    /** Quick note */', $docBlock);
    }

    public function test_it_handles_empty_properties(): void
    {
        $document = $this->createDocument('app.test.empty', [
            'type' => 'record',
            'properties' => [],
            'required' => [],
        ]);

        $docBlock = $this->generator->generateClassDocBlock($document, $document->getMainDefinition());

        $this->assertStringContainsString('Lexicon: app.test.empty', $docBlock);
        $this->assertStringNotContainsString('@property', $docBlock);
    }

    public function test_it_handles_nullable_properties(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'subtitle' => ['type' => 'string'],
            ],
            'required' => [],
        ]);

        $docBlock = $this->generator->generateClassDocBlock($document, $document->getMainDefinition());

        $this->assertStringContainsString('@property string|null $subtitle', $docBlock);
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

    /**
     * Helper to invoke protected method.
     *
     * @param  array<mixed>  $args
     * @return mixed
     */
    protected function invokeMethod(string $methodName, array $args)
    {
        $reflection = new \ReflectionClass($this->generator);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->generator, $args);
    }
}
