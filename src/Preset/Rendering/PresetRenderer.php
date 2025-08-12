<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset\Rendering;

use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Exception\TemplateRenderingException;
use Jmf\PresetRendering\Exception\UndefinedArrayKeyException;
use Jmf\PresetRendering\Exception\UnreadableObjectPropertyException;
use Jmf\PresetRendering\Preset\Preset;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use Stringable;
use Throwable;

readonly class PresetRenderer implements PresetRendererInterface
{
    public function __construct(
        private TemplateRendererInterface $templateRenderer,
        private ItemValueReader $itemPropertyReader,
        private HtmlEscaper $htmlEscaper,
    ) {
    }

    #[Override]
    public function render(
        Preset $preset,
        array | object $item,
        ?string $source = null,
    ): RenderedPreset {
        return new RenderedPreset(
            content:    $this->getContent($preset, $item, $source),
            properties: $preset->getProperties(),
        );
    }

    /**
     * @param array<string, mixed>|object $item
     *
     * @throws PresetRenderingException
     * @throws TemplateRenderingException
     * @throws UndefinedArrayKeyException
     * @throws UnreadableObjectPropertyException
     */
    private function getContent(
        Preset $preset,
        array | object $item,
        ?string $source,
    ): string {
        $valueFromSource = $this->tryGetValueFromSource($preset, $item, $source);
        $value           = $this->tryGetValueFromTemplate($preset, $item, $valueFromSource);
        $needsEscaping   = ($value === $valueFromSource);

        if (is_scalar($value) || ($value instanceof Stringable)) {
            $value = (string) $value;

            if ($needsEscaping) {
                return $this->htmlEscaper->escape($value);
            }

            return $value;
        }

        if (null === $value) {
            return '';
        }

        // @todo
        throw new PresetRenderingException('Unexpected content value type.');
    }

    /**
     * @param array<string, mixed>|object $item
     *
     * @throws UndefinedArrayKeyException
     * @throws UnreadableObjectPropertyException
     */
    private function tryGetValueFromSource(
        Preset $preset,
        array | object $item,
        ?string $source,
    ): mixed {
        return $this->itemPropertyReader->read($preset, $item, $source);
    }

    /**
     * @param array<string, mixed>|object $item
     *
     * @throws TemplateRenderingException
     */
    private function tryGetValueFromTemplate(
        Preset $preset,
        array | object $item,
        mixed $value,
    ): mixed {
        $template = $preset->getTemplate();

        if (null === $template) {
            return $value;
        }

        try {
            return $this->templateRenderer->render(
                $template,
                [
                    '_item'  => $item,
                    '_value' => $value,
                ],
            );
        } catch (Throwable $e) {
            throw new TemplateRenderingException(
                preset:   $preset,
                previous: $e,
            );
        }
    }
}
