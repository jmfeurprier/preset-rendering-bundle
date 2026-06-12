<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Property;

use Jmf\RenderingPreset\Exception\DuplicatePropertyException;
use Jmf\RenderingPreset\Property\Property;
use Jmf\RenderingPreset\Property\PropertyCollection;
use PHPUnit\Framework\TestCase;

final class PropertyCollectionTest extends TestCase
{
    public function testEmptyCollection(): void
    {
        $propertyCollection = new PropertyCollection([]);

        self::assertSame([], $propertyCollection->all());
    }

    public function testAll(): void
    {
        $foo = new Property('foo', false);
        $bar = new Property('bar', false);

        $propertyCollection = new PropertyCollection([$foo, $bar]);

        self::assertSame([$foo, $bar], $propertyCollection->all());
    }

    public function testDuplicatePropertyThrows(): void
    {
        $this->expectException(DuplicatePropertyException::class);

        new PropertyCollection([
            new Property('foo', false),
            new Property('foo', true),
        ]);
    }
}
