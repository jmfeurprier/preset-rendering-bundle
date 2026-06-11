<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset\Property;

use Jmf\RenderingPreset\Exception\PresetPropertyNotFoundException;
use Jmf\RenderingPreset\Preset\Property\PresetProperty;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use PHPUnit\Framework\TestCase;

final class PresetPropertyCollectionTest extends TestCase
{
    public function testEmptyCollection(): void
    {
        $presetPropertyCollection = new PresetPropertyCollection([]);

        self::assertSame([], $presetPropertyCollection->all());
        self::assertFalse($presetPropertyCollection->has('foo'));
    }

    public function testHas(): void
    {
        $presetPropertyCollection = new PresetPropertyCollection([new PresetProperty('foo', 'bar')]);

        self::assertTrue($presetPropertyCollection->has('foo'));
        self::assertFalse($presetPropertyCollection->has('baz'));
    }

    public function testGet(): void
    {
        $presetProperty   = new PresetProperty('foo', 'bar');
        $presetPropertyCollection = new PresetPropertyCollection([$presetProperty]);

        self::assertSame($presetProperty, $presetPropertyCollection->get('foo'));
    }

    public function testGetThrowsWhenNotFound(): void
    {
        $presetPropertyCollection = new PresetPropertyCollection([]);

        $this->expectException(PresetPropertyNotFoundException::class);

        $presetPropertyCollection->get('missing');
    }

    public function testTryGet(): void
    {
        $presetProperty   = new PresetProperty('foo', 'bar');
        $presetPropertyCollection = new PresetPropertyCollection([$presetProperty]);

        self::assertSame($presetProperty, $presetPropertyCollection->tryGet('foo'));
        self::assertNull($presetPropertyCollection->tryGet('missing'));
    }

    public function testGetValue(): void
    {
        $presetPropertyCollection = new PresetPropertyCollection([new PresetProperty('foo', 'bar')]);

        self::assertSame('bar', $presetPropertyCollection->getValue('foo'));
    }

    public function testTryGetValue(): void
    {
        $presetPropertyCollection = new PresetPropertyCollection([new PresetProperty('foo', 'bar')]);

        self::assertSame('bar', $presetPropertyCollection->tryGetValue('foo'));
        self::assertNull($presetPropertyCollection->tryGetValue('missing'));
        self::assertSame('default', $presetPropertyCollection->tryGetValue('missing', 'default'));
    }
}
