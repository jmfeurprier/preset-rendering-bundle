<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Tests\Preset\Property;

use Jmf\PresetRendering\Exception\NonUniquePropertyKeyException;
use Jmf\PresetRendering\Exception\PresetPropertyNotFoundException;
use Jmf\PresetRendering\Preset\Property\PresetProperty;
use Jmf\PresetRendering\Preset\Property\PresetPropertyCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PresetPropertyCollectionTest extends TestCase
{
    /**
     * @throws NonUniquePropertyKeyException
     */
    public function testEmptyCollection(): void
    {
        $presetPropertyCollection = new PresetPropertyCollection([]);

        self::assertSame([], $presetPropertyCollection->all());
    }

    /**
     * @return array{
     *     0: array<string, mixed>,
     *     1: array<string, mixed>,
     *     2: array<string, mixed>,
     * }[]
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
                ['foo' => 'bar'],
                ['foo' => 'baz'],
                ['foo' => 'bar'],
            ],
            [
                [
                    'foo' => '123',
                    'bar' => '234',
                ],
                [
                    'foo' => '345',
                    'baz' => '456',
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
     * @throws NonUniquePropertyKeyException
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

        $result = $primaryCollection->merge($secondaryCollection);

        self::assertCount(count($expected), $result->all());

        foreach ($expected as $key => $value) {
            self::assertSame($value, $result->get($key)->getValue());
        }
    }
}
