<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset;

use Jmf\RenderingPreset\Exception\CircularPresetParentException;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\InvalidPresetConfigurationException;
use Jmf\RenderingPreset\Exception\MissingRequiredPropertyValueException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\RenderingPreset\Exception\PropertyValueDomainException;

interface PresetRepositoryInterface
{
    /**
     * Resolves a single preset (and its parent chain) on demand, building only what is requested.
     *
     * @param non-empty-string $id
     *
     * @throws CircularPresetParentException
     * @throws InvalidConfigurationException
     * @throws InvalidPresetConfigurationException
     * @throws MissingRequiredPropertyValueException
     * @throws PresetNotFoundException
     * @throws PropertyValueDomainException
     */
    public function get(string $id): Preset;
}
