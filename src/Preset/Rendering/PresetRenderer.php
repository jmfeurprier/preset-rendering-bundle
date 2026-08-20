<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset\Rendering;

use BackedEnum;
use Jmf\RenderingPreset\Exception\HtmlEscapingException;
use Jmf\RenderingPreset\Exception\TemplateRenderingException;
use Jmf\RenderingPreset\Exception\UnexpectedContentValueTypeException;
use Jmf\RenderingPreset\Exception\UnreadableItemValueException;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use Stringable;
use Throwable;

readonly class PresetRenderer implements PresetRendererInterface
{
    public function __construct(
        private TemplateRendererInterface $templateRenderer,
        private ItemValueReader $itemValueReader,
        private HtmlEscaper $htmlEscaper,
    ) {
    }

    #[Override]
    public function render(
        Preset $preset,
        mixed $item,
        ?string $source = null,
    ): RenderedPreset {
        return new RenderedPreset(
            content:    $this->getContent($preset, $item, $source),
            properties: $preset->getProperties(),
        );
    }

    /**
     * @throws HtmlEscapingException
     * @throws TemplateRenderingException
     * @throws UnexpectedContentValueTypeException
     * @throws UnreadableItemValueException
     */
    private function getContent(
        Preset $preset,
        mixed $item,
        ?string $source,
    ): string {
        $valueFromSource = $this->tryGetValueFromSource($preset, $item, $source);
        $value           = $this->tryGetValueFromTemplate($preset, $item, $valueFromSource);
        $needsEscaping   = ($value === $valueFromSource);

        if ($value instanceof BackedEnum) {
            $value = $value->value;
        }

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

        throw new UnexpectedContentValueTypeException($preset, $item, $source, $value);
    }

    /**
     * @throws UnreadableItemValueException
     */
    private function tryGetValueFromSource(
        Preset $preset,
        mixed $item,
        ?string $source,
    ): mixed {
        return $this->itemValueReader->read($preset, $item, $source);
    }

    /**
     * @throws TemplateRenderingException
     */
    private function tryGetValueFromTemplate(
        Preset $preset,
        mixed $item,
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
