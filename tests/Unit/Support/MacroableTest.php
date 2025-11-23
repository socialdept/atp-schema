<?php

namespace SocialDept\Schema\Tests\Unit\Support;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Services\BlobHandler;
use SocialDept\Schema\Services\ModelMapper;
use SocialDept\Schema\Services\UnionResolver;
use SocialDept\Schema\Validation\Validator;

class MacroableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear any existing macros
        ModelMapper::flushMacros();
        BlobHandler::flushMacros();
        UnionResolver::flushMacros();
        Validator::flushMacros();
    }

    public function test_model_mapper_supports_macros(): void
    {
        ModelMapper::macro('customMethod', fn () => 'custom result');

        $mapper = new ModelMapper();

        $this->assertEquals('custom result', $mapper->customMethod());
    }

    public function test_model_mapper_macro_can_access_instance(): void
    {
        ModelMapper::macro('getCount', function () {
            return $this->count();
        });

        $mapper = new ModelMapper();

        $this->assertEquals(0, $mapper->getCount());
    }

    public function test_blob_handler_supports_macros(): void
    {
        BlobHandler::macro('customMethod', fn () => 'blob custom');

        $handler = new BlobHandler();

        $this->assertEquals('blob custom', $handler->customMethod());
    }

    public function test_blob_handler_macro_can_access_instance(): void
    {
        BlobHandler::macro('getCurrentDisk', function () {
            return $this->getDisk();
        });

        $handler = new BlobHandler('local');

        $this->assertEquals('local', $handler->getCurrentDisk());
    }

    public function test_union_resolver_supports_macros(): void
    {
        UnionResolver::macro('customMethod', fn () => 'union custom');

        $resolver = new UnionResolver();

        $this->assertEquals('union custom', $resolver->customMethod());
    }

    public function test_validator_supports_macros(): void
    {
        Validator::macro('customMethod', fn () => 'validator custom');

        $schemaLoader = $this->createMock(\SocialDept\Schema\Parser\SchemaLoader::class);
        $validator = new Validator($schemaLoader);

        $this->assertEquals('validator custom', $validator->customMethod());
    }

    public function test_macros_can_accept_parameters(): void
    {
        ModelMapper::macro('multiply', fn ($a, $b) => $a * $b);

        $mapper = new ModelMapper();

        $this->assertEquals(15, $mapper->multiply(3, 5));
    }

    public function test_macros_work_across_multiple_instances(): void
    {
        ModelMapper::macro('greet', fn ($name) => "Hello, {$name}!");

        $mapper1 = new ModelMapper();
        $mapper2 = new ModelMapper();

        $this->assertEquals('Hello, Alice!', $mapper1->greet('Alice'));
        $this->assertEquals('Hello, Bob!', $mapper2->greet('Bob'));
    }

    public function test_macro_can_return_instance_for_chaining(): void
    {
        ModelMapper::macro('chainable', function () {
            return $this;
        });

        $mapper = new ModelMapper();

        $result = $mapper->chainable()->chainable();

        $this->assertSame($mapper, $result);
    }

    public function test_has_macro_checks_existence(): void
    {
        ModelMapper::macro('exists', fn () => true);

        $this->assertTrue(ModelMapper::hasMacro('exists'));
        $this->assertFalse(ModelMapper::hasMacro('doesNotExist'));
    }

    public function test_flush_macros_removes_all(): void
    {
        ModelMapper::macro('test1', fn () => 'result1');
        ModelMapper::macro('test2', fn () => 'result2');

        $this->assertTrue(ModelMapper::hasMacro('test1'));
        $this->assertTrue(ModelMapper::hasMacro('test2'));

        ModelMapper::flushMacros();

        $this->assertFalse(ModelMapper::hasMacro('test1'));
        $this->assertFalse(ModelMapper::hasMacro('test2'));
    }

    public function test_macros_are_independent_between_classes(): void
    {
        ModelMapper::macro('sharedName', fn () => 'mapper');
        BlobHandler::macro('sharedName', fn () => 'handler');

        $mapper = new ModelMapper();
        $handler = new BlobHandler();

        $this->assertEquals('mapper', $mapper->sharedName());
        $this->assertEquals('handler', $handler->sharedName());
    }

    public function test_macro_can_use_closure_variables(): void
    {
        $prefix = 'PREFIX';

        ModelMapper::macro('withPrefix', fn ($value) => "{$prefix}: {$value}");

        $mapper = new ModelMapper();

        $this->assertEquals('PREFIX: test', $mapper->withPrefix('test'));
    }

    public function test_macro_can_modify_instance_state(): void
    {
        BlobHandler::macro('switchDisk', function ($disk) {
            $this->setDisk($disk);

            return $this;
        });

        $handler = new BlobHandler('local');

        $this->assertEquals('local', $handler->getDisk());

        $handler->switchDisk('s3');

        $this->assertEquals('s3', $handler->getDisk());
    }

    public function test_mixin_adds_multiple_methods(): void
    {
        $mixin = new class () {
            public function method1()
            {
                return fn () => 'method1';
            }

            public function method2()
            {
                return fn () => 'method2';
            }
        };

        ModelMapper::mixin($mixin);

        $mapper = new ModelMapper();

        $this->assertEquals('method1', $mapper->method1());
        $this->assertEquals('method2', $mapper->method2());
    }

    public function test_complex_macro_with_dependencies(): void
    {
        ModelMapper::macro('complexOperation', function ($data) {
            // Use existing methods
            $count = $this->count();

            return [
                'count' => $count,
                'data' => strtoupper($data),
            ];
        });

        $mapper = new ModelMapper();

        $result = $mapper->complexOperation('test');

        $this->assertEquals([
            'count' => 0,
            'data' => 'TEST',
        ], $result);
    }
}
