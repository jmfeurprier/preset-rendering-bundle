<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset\Property;

use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use PHPUnit\Framework\TestCase;

final class PresetPropertyCollectionTest extends TestCase
{
    public function testEmptyCollection(): void
    {
        $presetPropertyCollection = new PresetPropertyCollection([]);

        self::assertSame([], $presetPropertyCollection->all());
    }
}
