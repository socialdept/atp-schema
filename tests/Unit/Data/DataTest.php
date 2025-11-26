<?php

namespace SocialDept\AtpSchema\Tests\Unit\Data;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Data;

class DataTest extends TestCase
{
    public function test_it_converts_to_array(): void
    {
        $data = new TestData('John', 30);

        $array = $data->toArray();

        $this->assertSame([
            'name' => 'John',
            'age' => 30,
        ], $array);
    }

    public function test_it_converts_to_json(): void
    {
        $data = new TestData('John', 30);

        $json = $data->toJson();

        $this->assertJson($json);
        $this->assertSame('{"name":"John","age":30}', $json);
    }

    public function test_it_is_json_serializable(): void
    {
        $data = new TestData('John', 30);

        $json = json_encode($data);

        $this->assertSame('{"name":"John","age":30}', $json);
    }

    public function test_it_converts_to_string(): void
    {
        $data = new TestData('John', 30);

        $string = (string) $data;

        $this->assertSame('{"name":"John","age":30}', $string);
    }

    public function test_it_creates_from_array(): void
    {
        $data = TestData::fromArray([
            'name' => 'Jane',
            'age' => 25,
        ]);

        $this->assertSame('Jane', $data->name);
        $this->assertSame(25, $data->age);
    }

    public function test_it_creates_from_json(): void
    {
        $data = TestData::fromJson('{"name":"Bob","age":40}');

        $this->assertSame('Bob', $data->name);
        $this->assertSame(40, $data->age);
    }

    public function test_it_throws_on_invalid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON');

        TestData::fromJson('{invalid json}');
    }

    public function test_it_creates_from_record(): void
    {
        $data = TestData::fromRecord([
            'name' => 'Alice',
            'age' => 35,
        ]);

        $this->assertSame('Alice', $data->name);
        $this->assertSame(35, $data->age);
    }

    public function test_it_converts_to_record(): void
    {
        $data = new TestData('John', 30);

        $record = $data->toRecord();

        $this->assertSame([
            'name' => 'John',
            'age' => 30,
            '$type' => 'app.test.data',
        ], $record);
    }

    public function test_it_checks_equality(): void
    {
        $data1 = new TestData('John', 30);
        $data2 = new TestData('John', 30);
        $data3 = new TestData('Jane', 25);

        $this->assertTrue($data1->equals($data2));
        $this->assertFalse($data1->equals($data3));
    }

    public function test_it_generates_hash(): void
    {
        $data = new TestData('John', 30);

        $hash = $data->hash();

        $this->assertIsString($hash);
        $this->assertSame(64, strlen($hash)); // SHA256 produces 64 hex characters
    }

    public function test_it_generates_same_hash_for_equal_data(): void
    {
        $data1 = new TestData('John', 30);
        $data2 = new TestData('John', 30);

        $this->assertSame($data1->hash(), $data2->hash());
    }

    public function test_it_generates_different_hash_for_different_data(): void
    {
        $data1 = new TestData('John', 30);
        $data2 = new TestData('Jane', 25);

        $this->assertNotSame($data1->hash(), $data2->hash());
    }

    public function test_it_gets_property_dynamically(): void
    {
        $data = new TestData('John', 30);

        $this->assertSame('John', $data->__get('name'));
        $this->assertSame(30, $data->__get('age'));
    }

    public function test_it_throws_on_nonexistent_property(): void
    {
        $data = new TestData('John', 30);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property nonexistent does not exist');

        $data->__get('nonexistent');
    }

    public function test_it_checks_if_property_exists(): void
    {
        $data = new TestData('John', 30);

        $this->assertTrue($data->__isset('name'));
        $this->assertTrue($data->__isset('age'));
        $this->assertFalse($data->__isset('nonexistent'));
    }

    public function test_it_clones_with_modified_properties(): void
    {
        $data = new TestData('John', 30);

        $modified = $data->with(['age' => 31]);

        $this->assertSame('John', $modified->name);
        $this->assertSame(31, $modified->age);
        $this->assertNotSame($data, $modified);
        $this->assertSame(30, $data->age); // Original unchanged
    }

    public function test_it_serializes_nested_data_objects(): void
    {
        $nested = new TestData('Inner', 20);
        $parent = new TestDataWithNested('Outer', $nested);

        $array = $parent->toArray();

        $this->assertSame([
            'name' => 'Outer',
            'nested' => [
                'name' => 'Inner',
                'age' => 20,
                '$type' => 'app.test.data',
            ],
        ], $array);
    }

    public function test_it_serializes_arrays_of_data_objects(): void
    {
        $items = [
            new TestData('First', 10),
            new TestData('Second', 20),
        ];
        $collection = new TestDataWithArray('Collection', $items);

        $array = $collection->toArray();

        $this->assertSame([
            'name' => 'Collection',
            'items' => [
                ['name' => 'First', 'age' => 10, '$type' => 'app.test.data'],
                ['name' => 'Second', 'age' => 20, '$type' => 'app.test.data'],
            ],
        ], $array);
    }

    public function test_it_serializes_datetime_objects(): void
    {
        $date = new \DateTime('2024-01-01T12:00:00Z');
        $data = new TestDataWithDate('Event', $date);

        $array = $data->toArray();

        $this->assertArrayHasKey('createdAt', $array);
        $this->assertIsString($array['createdAt']);
        $this->assertStringContainsString('2024-01-01', $array['createdAt']);
    }

    public function test_it_returns_lexicon_nsid(): void
    {
        $lexicon = TestData::getLexicon();

        $this->assertSame('app.test.data', $lexicon);
    }

    public function test_validation_returns_true_when_helper_not_available(): void
    {
        $data = new TestData('John', 30);

        // Schema helper may not be available in unit tests
        $result = $data->validate();

        $this->assertTrue($result);
    }

    public function test_validation_errors_returns_empty_when_helper_not_available(): void
    {
        $data = new TestData('John', 30);

        // Schema helper may not be available in unit tests
        $errors = $data->validateWithErrors();

        $this->assertIsArray($errors);
    }
}

// Test implementations

class TestData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {
    }

    public static function getLexicon(): string
    {
        return 'app.test.data';
    }

    public static function fromArray(array $data): static
    {
        return new static(
            name: $data['name'],
            age: $data['age']
        );
    }
}

class TestDataWithNested extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly TestData $nested
    ) {
    }

    public static function getLexicon(): string
    {
        return 'app.test.nested';
    }

    public static function fromArray(array $data): static
    {
        return new static(
            name: $data['name'],
            nested: TestData::fromArray($data['nested'])
        );
    }
}

class TestDataWithArray extends Data
{
    /**
     * @param  array<TestData>  $items
     */
    public function __construct(
        public readonly string $name,
        public readonly array $items
    ) {
    }

    public static function getLexicon(): string
    {
        return 'app.test.collection';
    }

    public static function fromArray(array $data): static
    {
        return new static(
            name: $data['name'],
            items: array_map(
                fn ($item) => TestData::fromArray($item),
                $data['items']
            )
        );
    }
}

class TestDataWithDate extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly \DateTimeInterface $createdAt
    ) {
    }

    public static function getLexicon(): string
    {
        return 'app.test.dated';
    }

    public static function fromArray(array $data): static
    {
        return new static(
            name: $data['name'],
            createdAt: new \DateTime($data['createdAt'])
        );
    }
}
