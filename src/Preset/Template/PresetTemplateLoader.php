<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset\Template;

use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\TemplateRendering\FileTemplate;
use Jmf\TemplateRendering\StringTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Webmozart\Assert\Assert;

readonly class PresetTemplateLoader
{
    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws PresetRenderingException
     */
    public function load(
        array $presetConfig,
    ): ?TemplateInterface {
        Assert::isMap($presetConfig);

        $templateConfig = $presetConfig['template'] ?? null;

        if (null === $templateConfig) {
            return null;
        }

        if (is_string($templateConfig)) {
            return $this->loadStringTemplate($templateConfig);
        }

        if (is_array($templateConfig)) {
            return $this->loadFileTemplate($templateConfig);
        }

        // @todo
        throw new PresetRenderingException('Invalid Preset template configuration.');
    }

    private function loadStringTemplate(string $templateConfig): StringTemplate
    {
        Assert::stringNotEmpty($templateConfig);

        return new StringTemplate($templateConfig);
    }

    /**
     * @param array<mixed, mixed> $templateConfig
     */
    private function loadFileTemplate(array $templateConfig): FileTemplate
    {
        Assert::keyExists($templateConfig, 'path');

        $templatePath = $templateConfig['path'];

        Assert::stringNotEmpty($templatePath);

        return new FileTemplate($templatePath);
    }
}
