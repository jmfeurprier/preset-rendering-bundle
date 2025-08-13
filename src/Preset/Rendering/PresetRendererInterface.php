<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset\Rendering;

use Jmf\RenderingPreset\Exception\HtmlEscapingException;
use Jmf\RenderingPreset\Exception\TemplateRenderingException;
use Jmf\RenderingPreset\Exception\UnexpectedContentValueTypeException;
use Jmf\RenderingPreset\Exception\UnreadableItemValueException;
use Jmf\RenderingPreset\Preset\Preset;

interface PresetRendererInterface
{
    /**
     * @param array<string, mixed>|object $item
     *
     * @throws HtmlEscapingException
     * @throws TemplateRenderingException
     * @throws UnexpectedContentValueTypeException
     * @throws UnreadableItemValueException
     */
    public function render(
        Preset $preset,
        array | object $item,
        ?string $source = null,
    ): RenderedPreset;
}
