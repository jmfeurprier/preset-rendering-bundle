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
     * @throws HtmlEscapingException
     * @throws TemplateRenderingException
     * @throws UnexpectedContentValueTypeException
     * @throws UnreadableItemValueException
     */
    public function render(
        Preset $preset,
        mixed $item,
        ?string $source = null,
    ): RenderedPreset;
}
