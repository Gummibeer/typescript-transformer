<?php

namespace Spatie\TypeScriptTransformer\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Spatie\TypeScriptTransformer\Collectors\AnnotationCollector;
use Spatie\TypeScriptTransformer\Tests\FakeClasses\Test;
use Spatie\TypeScriptTransformer\Transformers\DtoTransformer;
use Spatie\TypeScriptTransformer\Transformers\MyclabsEnumTransformer;
use Spatie\TypeScriptTransformer\TypeScriptTransformer;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;

class IntegrationTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function it_works()
    {
        $temporaryDirectory = (new TemporaryDirectory())->create();

        $transformer = new TypeScriptTransformer(
            TypeScriptTransformerConfig::create()
                ->searchingPath(__DIR__ . '/FakeClasses/Integration')
                ->classPropertyReplacements([
                    DateTime::class => 'string',
                ])
                ->transformers([
                    MyclabsEnumTransformer::class,
                    DtoTransformer::class,
                ])
                ->collectors([
                    AnnotationCollector::class,
                ])
                ->outputFile($temporaryDirectory->path('types.d.ts'))
        );

        $transformer->transform();

        $transformed = file_get_contents($temporaryDirectory->path('types.d.ts'));

        $this->assertMatchesSnapshot($transformed);
    }
}
