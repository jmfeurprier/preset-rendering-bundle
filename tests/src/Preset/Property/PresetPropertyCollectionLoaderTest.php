<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset\Property;

use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollectionLoader;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyLoader;
use Jmf\RenderingPreset\Property\Property;
use Jmf\RenderingPreset\Property\PropertyCollection;
use Override;
use PHPUnit\Framework\TestCase;

final class PresetPropertyCollectionLoaderTest extends TestCase
{
    private PresetPropertyCollectionLoader $presetPropertyCollectionLoader;

    #[Override]
    protected function setUp(): void
    {
        $this->presetPropertyCollectionLoader = new PresetPropertyCollectionLoader(new PresetPropertyLoader());
    }

    public function testLoadWithNoProperties(): void
    {
        $propertyCollection = new PropertyCollection([]);

        $presetPropertyCollection = $this->presetPropertyCollectionLoader->load('myPreset', [], $propertyCollection);

        self::assertSame([], $presetPropertyCollection->all());
    }

    public function testLoadWithProperties(): void
    {
        $propertyCollection = new PropertyCollection([
            new Property('color', false, 'red'),
        ]);

        $presetPropertyCollection = $this->presetPropertyCollectionLoader->load(
            'myPreset',
            ['color' => 'blue'],
            $propertyCollection,
        );

        self::assertCount(1, $presetPropertyCollection->all());
        self::assertSame('blue', $presetPropertyCollection->getValue('color'));
    }

    public function testLoadUsesDefaultWhenKeyMissingFromConfig(): void
    {
        $propertyCollection = new PropertyCollection([
            new Property('color', false, 'red'),
        ]);

        $presetPropertyCollection = $this->presetPropertyCollectionLoader->load('myPreset', [], $propertyCollection);

        self::assertSame('red', $presetPropertyCollection->getValue('color'));
    }
}
