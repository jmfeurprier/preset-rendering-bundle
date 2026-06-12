<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset;

use Jmf\RenderingPreset\Preset\PresetTemplateLoader;
use Jmf\TemplateRendering\FileTemplate;
use Override;
use PHPUnit\Framework\TestCase;

final class PresetTemplateLoaderTest extends TestCase
{
    private PresetTemplateLoader $presetTemplateLoader;

    #[Override]
    protected function setUp(): void
    {
        $this->presetTemplateLoader = new PresetTemplateLoader();
    }

    public function testLoadReturnsNullWhenNoTemplateKey(): void
    {
        $result = $this->presetTemplateLoader->load([]);

        self::assertNull($result);
    }

    public function testLoadReturnsFileTemplate(): void
    {
        $result = $this->presetTemplateLoader->load(['template' => 'foo.html.twig']);

        self::assertInstanceOf(FileTemplate::class, $result);
    }
}
