<?php

namespace Spatie\TypescriptTransformer\Tests\Transformers;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Spatie\DataTransferObject\DataTransferObjectCollection;
use Spatie\Snapshots\MatchesSnapshots;
use Spatie\TypescriptTransformer\Structures\TypesCollection;
use Spatie\TypescriptTransformer\Tests\FakeClasses\Enum\RegularEnum;
use Spatie\TypescriptTransformer\Transformers\DtoCollectionTransformer;

class DtoCollectionTransformerTest extends TestCase
{
    use MatchesSnapshots;

    private DtoCollectionTransformer $transformer;

    private TypesCollection $typesCollection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->typesCollection = new TypesCollection();

        $this->transformer = new DtoCollectionTransformer($this->typesCollection);
    }

    /** @test */
    public function it_can_check_if_a_dto_collection_can_be_transformed()
    {
        $this->assertTrue(
            $this->transformer->canTransform(new ReflectionClass(new class extends DataTransferObjectCollection {
            }))
        );

        $this->assertFalse(
            $this->transformer->canTransform(new ReflectionClass(new class {
            }))
        );
    }

    /** @test */
    public function it_can_transform_a_dto_collection()
    {
        $type = $this->transformer->transform(
            new ReflectionClass(new class extends DataTransferObjectCollection {
                public function current(): string
                {
                    return parent::current();
                }
            }),
            'Test'
        );

        $this->assertMatchesTextSnapshot($type->transformed);
        $this->assertTrue($type->missingSymbols->isEmpty());
    }

    /** @test */
    public function it_can_transform_a_dto_collection_with_nullable_type()
    {
        $type = $this->transformer->transform(
            new ReflectionClass(new class extends DataTransferObjectCollection {
                public function current(): ?string
                {
                    return parent::current();
                }
            }),
            'Test'
        );

        $this->assertMatchesTextSnapshot($type->transformed);
        $this->assertTrue($type->missingSymbols->isEmpty());
    }

    /** @test */
    public function it_can_transform_a_dto_collection_with_missing_type()
    {
        $type = $this->transformer->transform(
            new ReflectionClass(new class extends DataTransferObjectCollection {
                public function current(): RegularEnum
                {
                    return parent::current();
                }
            }),
            'Test'
        );

        $this->assertMatchesTextSnapshot($type->transformed);
        $this->assertCount(1, $type->missingSymbols->all());
        $this->assertContains(RegularEnum::class, $type->missingSymbols->all());
    }
}