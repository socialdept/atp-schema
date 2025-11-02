<?php

namespace SocialDept\Schema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Generator\MethodGenerator;
use SocialDept\Schema\Parser\Nsid;

class MethodGeneratorTest extends TestCase
{
    protected MethodGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new MethodGenerator;
    }

    public function test_it_generates_get_lexicon_method(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [],
        ]);

        $method = $this->generator->generateGetLexicon($document);

        $this->assertStringContainsString('public static function getLexicon(): string', $method);
        $this->assertStringContainsString("return 'app.test.post';", $method);
    }

    public function test_it_generates_from_array_with_properties(): void
    {
        $document = $this->createDocument('app.test.user', [
            'type' => 'record',
            'properties' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
            ],
            'required' => ['name', 'age'],
        ]);

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('public static function fromArray(array $data): static', $method);
        $this->assertStringContainsString('return new static(', $method);
        $this->assertStringContainsString('name: $data[\'name\']', $method);
        $this->assertStringContainsString('age: $data[\'age\']', $method);
    }

    public function test_it_generates_from_array_with_optional_properties(): void
    {
        $document = $this->createDocument('app.test.user', [
            'type' => 'record',
            'properties' => [
                'name' => ['type' => 'string'],
                'nickname' => ['type' => 'string'],
            ],
            'required' => ['name'],
        ]);

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('name: $data[\'name\']', $method);
        $this->assertStringContainsString('nickname: $data[\'nickname\'] ?? null', $method);
    }

    public function test_it_handles_ref_types_in_from_array(): void
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

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('Author::fromArray($data[\'author\'])', $method);
    }

    public function test_it_handles_optional_ref_types(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'author' => [
                    'type' => 'ref',
                    'ref' => 'app.test.author',
                ],
            ],
            'required' => [],
        ]);

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('isset($data[\'author\']) ? Author::fromArray($data[\'author\']) : null', $method);
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

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('array_map(fn ($item) => Post::fromArray($item)', $method);
    }

    public function test_it_generates_empty_from_array_for_no_properties(): void
    {
        $document = $this->createDocument('app.test.empty', [
            'type' => 'record',
            'properties' => [],
            'required' => [],
        ]);

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('return new static();', $method);
    }

    public function test_it_generates_all_methods(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'text' => ['type' => 'string'],
            ],
            'required' => ['text'],
        ]);

        $methods = $this->generator->generateAll($document);

        $this->assertCount(2, $methods);
        $this->assertStringContainsString('getLexicon', $methods[0]);
        $this->assertStringContainsString('fromArray', $methods[1]);
    }

    public function test_it_generates_generic_method(): void
    {
        $method = $this->generator->generate(
            name: 'customMethod',
            returnType: 'string',
            body: '        return "hello";',
            description: 'A custom method',
            params: [
                ['name' => 'input', 'type' => 'string', 'description' => 'The input value'],
            ],
            isStatic: false
        );

        $this->assertStringContainsString('public function customMethod(string $input): string', $method);
        $this->assertStringContainsString('* A custom method', $method);
        $this->assertStringContainsString('* @param  string  $input  The input value', $method);
        $this->assertStringContainsString('* @return string', $method);
        $this->assertStringContainsString('return "hello";', $method);
    }

    public function test_it_generates_static_method(): void
    {
        $method = $this->generator->generate(
            name: 'create',
            returnType: 'static',
            body: '        return new static();',
            isStatic: true
        );

        $this->assertStringContainsString('public static function create(): static', $method);
    }

    public function test_it_generates_method_with_multiple_parameters(): void
    {
        $method = $this->generator->generate(
            name: 'calculate',
            returnType: 'int',
            body: '        return $a + $b;',
            params: [
                ['name' => 'a', 'type' => 'int'],
                ['name' => 'b', 'type' => 'int'],
            ]
        );

        $this->assertStringContainsString('function calculate(int $a, int $b): int', $method);
        $this->assertStringContainsString('@param  int  $a', $method);
        $this->assertStringContainsString('@param  int  $b', $method);
    }

    public function test_it_generates_method_without_return_type(): void
    {
        $method = $this->generator->generate(
            name: 'doSomething',
            returnType: '',
            body: '        // do something',
        );

        $this->assertStringContainsString('function doSomething()', $method);
        $this->assertStringNotContainsString('@return', $method);
    }

    public function test_it_generates_method_with_void_return(): void
    {
        $method = $this->generator->generate(
            name: 'process',
            returnType: 'void',
            body: '        // process',
        );

        $this->assertStringContainsString('function process(): void', $method);
        $this->assertStringNotContainsString('@return', $method);
    }

    public function test_it_handles_datetime_format(): void
    {
        $document = $this->createDocument('app.test.event', [
            'type' => 'record',
            'properties' => [
                'createdAt' => [
                    'type' => 'string',
                    'format' => 'datetime',
                ],
            ],
            'required' => ['createdAt'],
        ]);

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('new \\DateTime($data[\'createdAt\'])', $method);
    }

    public function test_it_handles_optional_datetime(): void
    {
        $document = $this->createDocument('app.test.event', [
            'type' => 'record',
            'properties' => [
                'updatedAt' => [
                    'type' => 'string',
                    'format' => 'datetime',
                ],
            ],
            'required' => [],
        ]);

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('isset($data[\'updatedAt\']) ? new \\DateTime($data[\'updatedAt\']) : null', $method);
    }

    public function test_it_handles_array_of_objects(): void
    {
        $document = $this->createDocument('app.test.config', [
            'type' => 'record',
            'properties' => [
                'settings' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                    ],
                ],
            ],
            'required' => [],
        ]);

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('$data[\'settings\'] ?? []', $method);
    }

    public function test_it_does_not_add_trailing_comma_to_last_assignment(): void
    {
        $document = $this->createDocument('app.test.user', [
            'type' => 'record',
            'properties' => [
                'first' => ['type' => 'string'],
                'last' => ['type' => 'string'],
            ],
            'required' => ['first', 'last'],
        ]);

        $method = $this->generator->generateFromArray($document);

        // Should have comma after first
        $this->assertMatchesRegularExpression('/last: \$data\[\'last\'\][^,]/', $method);
    }

    public function test_it_includes_method_docblocks(): void
    {
        $document = $this->createDocument('app.test.post', [
            'type' => 'record',
            'properties' => [
                'text' => ['type' => 'string'],
            ],
            'required' => ['text'],
        ]);

        $method = $this->generator->generateFromArray($document);

        $this->assertStringContainsString('/**', $method);
        $this->assertStringContainsString('* Create an instance from an array.', $method);
        $this->assertStringContainsString('* @param  array  $data', $method);
        $this->assertStringContainsString('* @return static', $method);
        $this->assertStringContainsString('*/', $method);
    }

    public function test_it_generates_to_model_method(): void
    {
        $method = $this->generator->generateToModel([
            'name' => ['type' => 'string'],
            'age' => ['type' => 'integer'],
        ], 'User');

        $this->assertStringContainsString('public function toModel(): User', $method);
        $this->assertStringContainsString('* Convert to a Laravel model instance.', $method);
        $this->assertStringContainsString('return new User([', $method);
        $this->assertStringContainsString("'name' => \$this->name,", $method);
        $this->assertStringContainsString("'age' => \$this->age,", $method);
    }

    public function test_it_generates_from_model_method(): void
    {
        $method = $this->generator->generateFromModel([
            'name' => ['type' => 'string'],
            'age' => ['type' => 'integer'],
        ], 'User');

        $this->assertStringContainsString('public static function fromModel(User $model): static', $method);
        $this->assertStringContainsString('* Create an instance from a Laravel model.', $method);
        $this->assertStringContainsString('return new static(', $method);
        $this->assertStringContainsString('name: $model->name ?? null,', $method);
        $this->assertStringContainsString('age: $model->age ?? null', $method);
    }

    public function test_it_gets_model_mapper(): void
    {
        $mapper = $this->generator->getModelMapper();

        $this->assertInstanceOf(\SocialDept\Schema\Generator\ModelMapper::class, $mapper);
    }

    /**
     * Helper to create a test document.
     *
     * @param  array<string, mixed>  $mainDef
     */
    protected function createDocument(string $nsid, array $mainDef): LexiconDocument
    {
        return new LexiconDocument(
            lexicon: 1,
            id: Nsid::parse($nsid),
            defs: ['main' => $mainDef],
            description: null,
            source: null,
            raw: [
                'lexicon' => 1,
                'id' => $nsid,
                'defs' => ['main' => $mainDef],
            ]
        );
    }
}
