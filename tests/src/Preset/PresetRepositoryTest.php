<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset;

use Jmf\RenderingPreset\Exception\CircularPresetParentException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\RenderingPreset\Preset\PresetLoader;
use Jmf\RenderingPreset\Preset\PresetMerger;
use Jmf\RenderingPreset\Preset\PresetRepository;
use Jmf\RenderingPreset\Preset\PresetTemplateLoader;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollectionLoader;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollectionMerger;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyLoader;
use Jmf\RenderingPreset\Property\PropertyCollection;
use Override;
use PHPUnit\Framework\TestCase;

final class PresetRepositoryTest extends TestCase
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

    public function testGetReturnsPreset(): void
    {
        $presetRepository = new PresetRepository(
            $this->presetLoader,
            $this->propertyCollection,
            ['foo' => []],
        );

        $preset = $presetRepository->get('foo');

        self::assertSame('foo', $preset->getId());
    }

    public function testGetMemoizes(): void
    {
        $presetRepository = new PresetRepository(
            $this->presetLoader,
            $this->propertyCollection,
            ['foo' => []],
        );

        $preset1 = $presetRepository->get('foo');
        $preset2 = $presetRepository->get('foo');

        self::assertSame($preset1, $preset2);
    }

    public function testGetThrowsOnUnknownId(): void
    {
        $presetRepository = new PresetRepository(
            $this->presetLoader,
            $this->propertyCollection,
            [],
        );

        $this->expectException(PresetNotFoundException::class);

        $presetRepository->get('unknown');
    }

    public function testGetThrowsOnCircularParent(): void
    {
        $presetRepository = new PresetRepository(
            $this->presetLoader,
            $this->propertyCollection,
            [
                'A' => ['parent' => 'B'],
                'B' => ['parent' => 'A'],
            ],
        );

        $this->expectException(CircularPresetParentException::class);

        $presetRepository->get('A');
    }
}
