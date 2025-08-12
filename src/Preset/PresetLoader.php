<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset;

use Jmf\PresetRendering\Exception\NonUniquePropertyKeyException;
use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Preset\Property\PresetPropertyCollection;
use Jmf\PresetRendering\Preset\Property\PresetPropertyCollectionLoader;
use Jmf\PresetRendering\Preset\Template\PresetTemplateLoader;
use Jmf\PresetRendering\Property\PropertyCollection;
use Jmf\TemplateRendering\TemplateInterface;
use Webmozart\Assert\Assert;

readonly class PresetLoader
{
    public function __construct(
        private PresetTemplateLoader $presetTemplateLoader,
        private PresetPropertyCollectionLoader $presetPropertyCollectionLoader,
        private PresetMerger $presetMerger,
    ) {
    }

    /**
     * @param array<string, mixed> $presetsConfig
     * @param non-empty-string     $presetId
     *
     * @throws NonUniquePropertyKeyException
     * @throws PresetRenderingException
     */
    public function load(
        array $presetsConfig,
        string $presetId,
        PropertyCollection $propertyCollection,
    ): Preset {
        Assert::isMap($presetsConfig);
        Assert::stringNotEmpty($presetId);

        return $this->doLoad($presetsConfig, $presetId, $propertyCollection);
    }

    /**
     * @param array<string, mixed> $presetsConfig
     * @param non-empty-string     $presetId
     *
     * @throws NonUniquePropertyKeyException
     * @throws PresetRenderingException
     */
    private function doLoad(
        array $presetsConfig,
        string $presetId,
        PropertyCollection $propertyCollection,
    ): Preset {
        Assert::keyExists($presetsConfig, $presetId);

        $presetConfig = $presetsConfig[$presetId];

        Assert::isMap($presetConfig);

        $preset = new Preset(
            id:         $presetId,
            source:     $this->getSource($presetConfig),
            template:   $this->getTemplate($presetConfig),
            properties: $this->getPresetPropertyCollection($presetConfig, $propertyCollection),
        );

        $parent = $this->getParent($presetsConfig, $presetConfig, $propertyCollection);

        if (null === $parent) {
            return $preset;
        }

        return $this->presetMerger->merge($preset, $parent);
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @return null|non-empty-string
     */
    private function getSource(array $presetConfig): ?string
    {
        $source = $presetConfig['source'] ?? null;

        Assert::nullOrStringNotEmpty($source);

        return $source;
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws PresetRenderingException
     */
    private function getTemplate(array $presetConfig): ?TemplateInterface
    {
        return $this->presetTemplateLoader->load($presetConfig);
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws NonUniquePropertyKeyException
     * @throws PresetRenderingException
     */
    private function getPresetPropertyCollection(
        array $presetConfig,
        PropertyCollection $propertyCollection,
    ): PresetPropertyCollection {
        return $this->presetPropertyCollectionLoader->load(
            $presetConfig,
            $propertyCollection,
        );
    }

    /**
     * @param array<string, mixed> $presetsConfig
     * @param array<string, mixed> $presetConfig
     *
     * @throws NonUniquePropertyKeyException
     * @throws PresetRenderingException
     */
    private function getParent(
        array $presetsConfig,
        array $presetConfig,
        PropertyCollection $propertyCollection,
    ): ?Preset {
        $parentId = $presetConfig['parent'] ?? null;

        Assert::nullOrStringNotEmpty($parentId);

        if (null === $parentId) {
            return null;
        }

        return $this->doLoad($presetsConfig, $parentId, $propertyCollection);
    }
}
