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
        $collection = new PresetPropertyCollection([]);

        self::assertSame([], $collection->all());
        self::assertFalse($collection->has('foo'));
    }

    public function testHas(): void
    {
        $collection = new PresetPropertyCollection([new PresetProperty('foo', 'bar')]);

        self::assertTrue($collection->has('foo'));
        self::assertFalse($collection->has('baz'));
    }

    public function testGet(): void
    {
        $property   = new PresetProperty('foo', 'bar');
        $collection = new PresetPropertyCollection([$property]);

        self::assertSame($property, $collection->get('foo'));
    }

    public function testGetThrowsWhenNotFound(): void
    {
        $collection = new PresetPropertyCollection([]);

        $this->expectException(PresetPropertyNotFoundException::class);

        $collection->get('missing');
    }

    public function testTryGet(): void
    {
        $property   = new PresetProperty('foo', 'bar');
        $collection = new PresetPropertyCollection([$property]);

        self::assertSame($property, $collection->tryGet('foo'));
        self::assertNull($collection->tryGet('missing'));
    }

    public function testGetValue(): void
    {
        $collection = new PresetPropertyCollection([new PresetProperty('foo', 'bar')]);

        self::assertSame('bar', $collection->getValue('foo'));
    }

    public function testTryGetValue(): void
    {
        $collection = new PresetPropertyCollection([new PresetProperty('foo', 'bar')]);

        self::assertSame('bar', $collection->tryGetValue('foo'));
        self::assertNull($collection->tryGetValue('missing'));
        self::assertSame('default', $collection->tryGetValue('missing', 'default'));
    }
}
