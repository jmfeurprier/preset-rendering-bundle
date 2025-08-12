<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Twig;

use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Preset\PresetRepository;
use Jmf\PresetRendering\Preset\Rendering\PresetRendererInterface;
use Jmf\PresetRendering\Preset\Rendering\RenderedPreset;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PresetRenderingExtension extends AbstractExtension
{
    public final const string PREFIX_DEFAULT = '';

    public function __construct(
        private readonly PresetRepository $presetRepository,
        private readonly PresetRendererInterface $presetRenderer,
        private readonly string $prefix = self::PREFIX_DEFAULT,
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    #[Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                $this->prefix . 'preset_render',
                $this->render(...),
                [
                    'is_safe' => ['html'],
                ],
            ),
            new TwigFunction(
                $this->prefix . 'preset_get',
                $this->get(...),
                [
                    'is_safe' => ['html'],
                ],
            ),
        ];
    }

    /**
     * @param non-empty-string            $presetId
     * @param array<string, mixed>|object $item
     * @param null|non-empty-string       $source
     *
     * @throws PresetRenderingException
     */
    public function render(
        string $presetId,
        array | object $item,
        ?string $source = null,
    ): string {
        return $this->get($presetId, $item, $source)->getContent();
    }

    /**
     * @param non-empty-string            $presetId
     * @param array<string, mixed>|object $item
     * @param null|non-empty-string       $source
     *
     * @throws PresetRenderingException
     */
    public function get(
        string $presetId,
        array | object $item,
        ?string $source = null,
    ): RenderedPreset {
        $preset = $this->presetRepository->get($presetId);

        return $this->presetRenderer->render($preset, $item, $source);
    }
}
