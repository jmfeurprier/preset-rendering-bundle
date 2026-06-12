<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset;

use Jmf\RenderingPreset\Exception\InvalidPresetConfigurationException;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\PresetLoader;
use Jmf\RenderingPreset\Preset\PresetMerger;
use Jmf\RenderingPreset\Preset\PresetTemplateLoader;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollectionLoader;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollectionMerger;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyLoader;
use Jmf\RenderingPreset\Property\PropertyCollection;
use Jmf\TemplateRendering\FileTemplate;
use Override;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class PresetLoaderTest extends TestCase
{
    private PresetLoader $presetLoader;

    private PropertyCollection $propertyCollection;

    #[Override]
    protected function setUp(): void
    {
        $this->presetLoader = new PresetLoader(
            new PresetTemplateLoader(),
            new PresetPropertyCollectionLoader(new PresetPropertyLoader()),
            new PresetMerger(new PresetPropertyCollectionMerger()),
        );

        $this->propertyCollection = new PropertyCollection([]);
    }

    public function testLoadBare(): void
    {
        $preset = $this->presetLoader->load(
            [],
            'myId',
            $this->propertyCollection,
            fn() => throw new RuntimeException('parent resolver should not be called'),
        );

        self::assertSame('myId', $preset->getId());
        self::assertNull($preset->getSource());
        self::assertNull($preset->getTemplate());
    }

    public function testLoadWithSource(): void
    {
        $preset = $this->presetLoader->load(
            ['source' => 'mySource'],
            'myId',
            $this->propertyCollection,
            fn() => throw new RuntimeException('parent resolver should not be called'),
        );

        self::assertSame('mySource', $preset->getSource());
    }

    public function testLoadWithTemplate(): void
    {
        $preset = $this->presetLoader->load(
            ['template' => 'foo.html.twig'],
            'myId',
            $this->propertyCollection,
            fn() => throw new RuntimeException('parent resolver should not be called'),
        );

        self::assertInstanceOf(FileTemplate::class, $preset->getTemplate());
    }

    public function testLoadWithParentInheritsSource(): void
    {
        $parent = new Preset(
            id:         'parentId',
            source:     'parentSource',
            template:   null,
            properties: new PresetPropertyCollection([]),
        );

        $preset = $this->presetLoader->load(
            ['parent' => 'parentId'],
            'childId',
            $this->propertyCollection,
            fn(): Preset => $parent,
        );

        self::assertSame('childId', $preset->getId());
        self::assertSame('parentSource', $preset->getSource());
    }

    public function testLoadWithParentChildSourceTakesPrecedence(): void
    {
        $parent = new Preset(
            id:         'parentId',
            source:     'parentSource',
            template:   null,
            properties: new PresetPropertyCollection([]),
        );

        $preset = $this->presetLoader->load(
            ['source' => 'childSource', 'parent' => 'parentId'],
            'childId',
            $this->propertyCollection,
            fn(): Preset => $parent,
        );

        self::assertSame('childSource', $preset->getSource());
    }

    public function testLoadListConfigThrows(): void
    {
        $this->expectException(InvalidPresetConfigurationException::class);

        $listConfig = ['a', 'b'];

        $this->presetLoader->load(
            $listConfig, // @phpstan-ignore argument.type
            'myId',
            $this->propertyCollection,
            fn() => throw new RuntimeException(),
        );
    }
}
