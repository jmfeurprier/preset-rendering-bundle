<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Configuration\Preset\Template;

use Jmf\TemplateRendering\FileTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Webmozart\Assert\Assert;

readonly class PresetTemplateLoader
{
    /**
     * @param array<string, mixed> $presetConfig
     */
    public function load(
        array $presetConfig,
    ): ?TemplateInterface {
        Assert::isMap($presetConfig);

        $templatePath = $presetConfig['template'] ?? null;

        if (null === $templatePath) {
            return null;
        }

        Assert::stringNotEmpty($templatePath);

        return new FileTemplate($templatePath);
    }
}
