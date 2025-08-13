<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset;

use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\RenderingPreset\Exception\RenderingPresetException;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\PresetCollection;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use PHPUnit\Framework\TestCase;

final class PresetCollectionTest extends TestCase
{
    /**
     * @throws PresetNotFoundException
     * @throws RenderingPresetException
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
     * @param non-empty-string $id
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
