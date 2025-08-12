<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Tests\Preset;

use Jmf\PresetRendering\Exception\NonUniquePropertyKeyException;
use Jmf\PresetRendering\Exception\PresetNotFoundException;
use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Preset\Preset;
use Jmf\PresetRendering\Preset\PresetCollection;
use Jmf\PresetRendering\Preset\Property\PresetPropertyCollection;
use PHPUnit\Framework\TestCase;

final class PresetCollectionTest extends TestCase
{
    /**
     * @throws NonUniquePropertyKeyException
     * @throws PresetNotFoundException
     * @throws PresetRenderingException
     */
    public function testPresetIsRetrievable(): void
    {
        $presetPrimary   = $this->createPreset('foo');
        $presetSecondary = $this->createPreset('bar');

        $presetCollection = new PresetCollection(
            [
                $presetPrimary,
                $presetSecondary,
            ],
        );

        self::assertSame($presetPrimary, $presetCollection->get('foo'));
        self::assertSame($presetSecondary, $presetCollection->get('bar'));
    }

    /**
     * @throws NonUniquePropertyKeyException
     * @throws PresetRenderingException
     */
    public function testNonUniquePresetIdIsDetected(): void
    {
        $presetPrimary   = $this->createPreset('foo');
        $presetSecondary = $this->createPreset('foo');

        $this->expectException(PresetRenderingException::class);

        new PresetCollection(
            [
                $presetPrimary,
                $presetSecondary,
            ],
        );
    }

    /**
     * @param non-empty-string $id
     *
     * @throws NonUniquePropertyKeyException
     */
    private function createPreset(string $id): Preset
    {
        return new Preset(
            id:         $id,
            source:     null,
            template:   null,
            properties: new PresetPropertyCollection([]),
        );
    }
}
