<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Configuration\Preset;

use Jmf\RenderingPreset\Configuration\Preset\Property\PresetPropertyCollectionLoader;
use Jmf\RenderingPreset\Configuration\Preset\Template\PresetTemplateLoader;
use Jmf\RenderingPreset\Exception\MissingRequiredPropertyValueException;
use Jmf\RenderingPreset\Exception\PropertyValueDomainException;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\RenderingPreset\Property\PropertyCollection;
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
     * @throws MissingRequiredPropertyValueException
     * @throws PropertyValueDomainException
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
     * @throws MissingRequiredPropertyValueException
     * @throws PropertyValueDomainException
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
            properties: $this->getPresetPropertyCollection($presetId, $presetConfig, $propertyCollection),
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
     */
    private function getTemplate(
        array $presetConfig,
    ): ?TemplateInterface {
        return $this->presetTemplateLoader->load($presetConfig);
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws MissingRequiredPropertyValueException
     * @throws PropertyValueDomainException
     */
    private function getPresetPropertyCollection(
        string $presetId,
        array $presetConfig,
        PropertyCollection $propertyCollection,
    ): PresetPropertyCollection {
        return $this->presetPropertyCollectionLoader->load(
            $presetId,
            $presetConfig,
            $propertyCollection,
        );
    }

    /**
     * @param array<string, mixed> $presetsConfig
     * @param array<string, mixed> $presetConfig
     *
     * @throws MissingRequiredPropertyValueException
     * @throws PropertyValueDomainException
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
