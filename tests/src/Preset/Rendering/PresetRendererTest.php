<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset\Rendering;

use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\RenderingPreset\Preset\Rendering\HtmlEscaper;
use Jmf\RenderingPreset\Preset\Rendering\ItemValueReader;
use Jmf\RenderingPreset\Preset\Rendering\PresetRenderer;
use Jmf\TemplateRendering\FileTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class PresetRendererTest extends TestCase
{
    private TemplateRendererInterface & MockObject $templateRenderer;

    private PresetRenderer $presetRenderer;

    #[Override]
    protected function setUp(): void
    {
        $this->templateRenderer = $this->createMock(TemplateRendererInterface::class);

        $this->presetRenderer = new PresetRenderer(
            $this->templateRenderer,
            new ItemValueReader(PropertyAccess::createPropertyAccessor()),
            new HtmlEscaper($this->templateRenderer),
        );
    }

    public function testRenderWithNoSourceNoTemplate(): void
    {
        $this->templateRenderer
            ->expects(self::never())
            ->method('renderFromString');

        $preset = $this->makePreset(null, null);

        $renderedPreset = $this->presetRenderer->render($preset, []);

        self::assertSame('', $renderedPreset->getContent());
    }

    public function testRenderWithSourceNoTemplate(): void
    {
        $this->templateRenderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('{{ _value }}', ['_value' => 'Alice'])
            ->willReturn('Alice');

        $preset = $this->makePreset('name', null);

        $renderedPreset = $this->presetRenderer->render($preset, ['name' => 'Alice']);

        self::assertSame('Alice', $renderedPreset->getContent());
    }

    public function testRenderWithTemplate(): void
    {
        $fileTemplate = new FileTemplate('foo.html.twig');

        $this->templateRenderer
            ->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo($fileTemplate),
                self::arrayHasKey('_value'),
            )
            ->willReturn('<b>Alice</b>');

        $this->templateRenderer
            ->expects(self::never())
            ->method('renderFromString');

        $preset = $this->makePreset('name', $fileTemplate);

        $renderedPreset = $this->presetRenderer->render($preset, ['name' => 'Alice']);

        self::assertSame('<b>Alice</b>', $renderedPreset->getContent());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testRenderReturnsPresetProperties(): void
    {
        $this->templateRenderer
            ->method('renderFromString')
            ->willReturn('Alice');

        $presetPropertyCollection = new PresetPropertyCollection([]);
        $preset     = new Preset(
            id:         'test',
            source:     'name',
            template:   null,
            properties: $presetPropertyCollection,
        );

        $renderedPreset = $this->presetRenderer->render($preset, ['name' => 'Alice']);

        self::assertSame($presetPropertyCollection, $renderedPreset->getProperties());
    }

    /**
     * @param null|non-empty-string $source
     */
    private function makePreset(?string $source, ?TemplateInterface $template): Preset
    {
        return new Preset(
            id:         'test',
            source:     $source,
            template:   $template,
            properties: new PresetPropertyCollection([]),
        );
    }
}
