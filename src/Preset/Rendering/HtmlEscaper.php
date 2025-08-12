<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset\Rendering;

use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Throwable;

readonly class HtmlEscaper
{
    public function __construct(
        private TemplateRendererInterface $templateRenderer,
    ) {
    }

    /**
     * @throws PresetRenderingException
     */
    public function escape(
        string $value,
    ): string {
        try {
            return $this->templateRenderer->renderFromString(
                '{{ _value }}',
                [
                    '_value' => $value,
                ],
            );
        } catch (Throwable $e) {
            // @todo
            throw new PresetRenderingException(
                previous: $e,
            );
        }
    }
}
