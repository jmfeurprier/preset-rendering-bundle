<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset\Property;

use Jmf\RenderingPreset\Exception\PresetPropertyNotFoundException;
use Jmf\RenderingPreset\Preset\Property\PresetProperty;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollectionMerger;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PresetPropertyCollectionMergerTest extends TestCase
{
    private PresetPropertyCollectionMerger $presetPropertyCollectionMerger;

    #[Override]
    protected function setUp(): void
    {
        $this->presetPropertyCollectionMerger = new PresetPropertyCollectionMerger();
    }

    /**
     * @return array<string, mixed>[][]
     */
    public static function dataProviderMerge(): iterable
    {
        return [
            [
                [],
                [],
                [],
            ],
            [
                ['foo' => 'bar'],
                [],
                ['foo' => 'bar'],
            ],
            [
                [],
                ['foo' => 'bar'],
                ['foo' => 'bar'],
            ],
            [
                ['foo' => 'baz'],
                ['foo' => 'bar'],
                ['foo' => 'bar'],
            ],
            [
                [
                    'foo' => '345',
                    'baz' => '456',
                ],
                [
                    'foo' => '123',
                    'bar' => '234',
                ],
                [
                    'foo' => '123',
                    'bar' => '234',
                    'baz' => '456',
                ],
            ],
        ];
    }

    /**
     * @param array<non-empty-string, mixed> $primaryValues
     * @param array<non-empty-string, mixed> $secondaryValues
     * @param array<non-empty-string, mixed> $expected
     *
     * @throws PresetPropertyNotFoundException
     */
    #[DataProvider('dataProviderMerge')]
    public function testMergeWithEmptyCollections(
        array $primaryValues,
        array $secondaryValues,
        array $expected,
    ): void {
        $primaryProperties = [];
        foreach ($primaryValues as $key => $value) {
            $primaryProperties[] = new PresetProperty($key, $value);
        }

        $secondaryProperties = [];
        foreach ($secondaryValues as $key => $value) {
            $secondaryProperties[] = new PresetProperty($key, $value);
        }

        $primaryCollection   = new PresetPropertyCollection($primaryProperties);
        $secondaryCollection = new PresetPropertyCollection($secondaryProperties);

        $result = $this->presetPropertyCollectionMerger->merge($primaryCollection, $secondaryCollection);

        self::assertCount(count($expected), $result->all());

        foreach ($expected as $key => $value) {
            self::assertSame($value, $result->get($key)->getValue());
        }
    }
}
