<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Property;

use Jmf\RenderingPreset\Property\PropertyCollectionLoader;
use Jmf\RenderingPreset\Property\PropertyLoader;
use Override;
use PHPUnit\Framework\TestCase;

final class PropertyCollectionLoaderTest extends TestCase
{
    private PropertyCollectionLoader $propertyCollectionLoader;

    #[Override]
    protected function setUp(): void
    {
        $this->propertyCollectionLoader = new PropertyCollectionLoader(new PropertyLoader());
    }

    public function testLoadEmpty(): void
    {
        $propertyCollection = $this->propertyCollectionLoader->load([]);

        self::assertSame([], $propertyCollection->all());
    }

    public function testLoad(): void
    {
        $propertyCollection = $this->propertyCollectionLoader->load([
            'color' => [
                'required' => false,
                'default'  => 'red',
            ],
        ]);

        $properties = array_values(iterator_to_array($propertyCollection->all()));

        self::assertCount(1, $properties);
        self::assertSame('color', $properties[0]->getKey());
        self::assertFalse($properties[0]->isRequired());
        self::assertSame('red', $properties[0]->getDefault());
    }
}
