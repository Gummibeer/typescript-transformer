<?php

namespace Spatie\TypeScriptTransformer\Tests\ClassPropertyProcessors;

use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Array_;
use PHPUnit\Framework\TestCase;
use Spatie\TypeScriptTransformer\ClassPropertyProcessors\DtoCollectionClassPropertyProcessor;
use Spatie\TypeScriptTransformer\Support\UnknownType;
use Spatie\TypeScriptTransformer\Tests\FakeClasses\Collections\DtoCollection;
use Spatie\TypeScriptTransformer\Tests\FakeClasses\Collections\NullableDtoCollection;
use Spatie\TypeScriptTransformer\Tests\FakeClasses\Collections\StringDtoCollection;
use Spatie\TypeScriptTransformer\Tests\FakeClasses\Collections\UntypedDtoCollection;
use Spatie\TypeScriptTransformer\Tests\Fakes\FakeReflectionProperty;

class DtoCollectionClassPropertyProcessorTest extends TestCase
{
    private DtoCollectionClassPropertyProcessor $processor;

    private TypeResolver $typeResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->typeResolver = new TypeResolver();

        $this->processor = new DtoCollectionClassPropertyProcessor();
    }

    /** @test */
    public function it_will_process_a_dto_collection()
    {
        $type = $this->processor->process(
            $this->typeResolver->resolve(DtoCollection::class),
            FakeReflectionProperty::create()
        );

        $this->assertEquals(
            '\Spatie\TypeScriptTransformer\Tests\FakeClasses\Integration\Dto[]',
            (string) $type
        );
    }

    /** @test */
    public function it_will_process_a_nullable_dto_collection()
    {
        $type = $this->processor->process(
            $this->typeResolver->resolve(NullableDtoCollection::class),
            FakeReflectionProperty::create()
        );

        $this->assertEquals(
            '?\Spatie\TypeScriptTransformer\Tests\FakeClasses\Integration\Dto[]',
            (string) $type
        );
    }

    /** @test */
    public function it_will_process_a_dto_collection_with_built_in_type()
    {
        $type = $this->processor->process(
            $this->typeResolver->resolve(StringDtoCollection::class),
            FakeReflectionProperty::create()
        );

        $this->assertEquals('string[]', (string) $type);
    }

    /** @test */
    public function it_will_process_a_dto_collection_without_type()
    {
        $type = $this->processor->process(
            $this->typeResolver->resolve(UntypedDtoCollection::class),
            FakeReflectionProperty::create()
        );

        $this->assertEquals(new Array_(new UnknownType()), $type);
    }

    /** @test */
    public function it_will_pass_non_dto_collections()
    {
        $type = $this->processor->process(
            $this->typeResolver->resolve('string'),
            FakeReflectionProperty::create()
        );

        $this->assertEquals('string', (string) $type);
    }
}
