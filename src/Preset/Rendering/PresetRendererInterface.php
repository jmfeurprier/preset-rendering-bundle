<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset\Rendering;

use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Preset\Preset;

interface PresetRendererInterface
{
    /**
     * @param array<string, mixed>|object $item
     *
     * @throws PresetRenderingException
     */
    public function render(
        Preset $preset,
        array | object $item,
        ?string $source = null,
    ): RenderedPreset;
}
