<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset\Rendering;

use Jmf\RenderingPreset\Exception\UnexpectedContentValueTypeException;
use Jmf\RenderingPreset\Exception\TemplateRenderingException;
use Jmf\RenderingPreset\Exception\UnexpectedItemSourceException;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\RenderingPreset\Preset\Rendering\HtmlEscaper;
use Jmf\RenderingPreset\Preset\Rendering\ItemValueReader;
use Jmf\RenderingPreset\Preset\Rendering\PresetRenderer;
use Jmf\RenderingPreset\Tests\Fixture\PureEnum;
use Jmf\RenderingPreset\Tests\Fixture\StatusEnum;
use Jmf\TemplateRendering\FileTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
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

    #[AllowMockObjectsWithoutExpectations]
    public function testRenderWithNoSourceNoTemplateThrowsOnArrayItem(): void
    {
        $preset = $this->makePreset(null, null);

        $this->expectException(UnexpectedContentValueTypeException::class);

        $this->presetRenderer->render($preset, ['name' => 'Alice']);
    }

    public function testRenderWithNoSourceNoTemplateRendersScalarItem(): void
    {
        $this->templateRenderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('{{ _value }}', ['_value' => 'Alice'])
            ->willReturn('Alice');

        $preset = $this->makePreset(null, null);

        $renderedPreset = $this->presetRenderer->render($preset, 'Alice');

        self::assertSame('Alice', $renderedPreset->getContent());
    }

    public function testRenderWithNoSourcePassesObjectItemAsValue(): void
    {
        $fileTemplate = new FileTemplate('foo.html.twig');
        $item         = new \stdClass();

        $this->templateRenderer
            ->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo($fileTemplate),
                [
                    '_item'  => $item,
                    '_value' => $item,
                ],
            )
            ->willReturn('3 years');

        $this->templateRenderer
            ->expects(self::never())
            ->method('renderFromString');

        $preset = $this->makePreset(null, $fileTemplate);

        $renderedPreset = $this->presetRenderer->render($preset, $item);

        self::assertSame('3 years', $renderedPreset->getContent());
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

    public function testRenderWithScalarItemIgnoresPresetSource(): void
    {
        $this->templateRenderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('{{ _value }}', ['_value' => '12.5'])
            ->willReturn('12.5');

        $preset = $this->makePreset('price', null);

        $renderedPreset = $this->presetRenderer->render($preset, 12.5);

        self::assertSame('12.5', $renderedPreset->getContent());
    }

    public function testRenderWithScalarItemAndTemplate(): void
    {
        $fileTemplate = new FileTemplate('foo.html.twig');

        $this->templateRenderer
            ->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo($fileTemplate),
                [
                    '_item'  => 12.5,
                    '_value' => 12.5,
                ],
            )
            ->willReturn('12.50 EUR');

        $this->templateRenderer
            ->expects(self::never())
            ->method('renderFromString');

        $preset = $this->makePreset(null, $fileTemplate);

        $renderedPreset = $this->presetRenderer->render($preset, 12.5);

        self::assertSame('12.50 EUR', $renderedPreset->getContent());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testRenderWithScalarItemAndExplicitSourceThrows(): void
    {
        $preset = $this->makePreset(null, null);

        $this->expectException(UnexpectedItemSourceException::class);

        $this->presetRenderer->render($preset, 12.5, 'price');
    }

    public function testRenderEscapesValueReadFromSource(): void
    {
        $this->templateRenderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('{{ _value }}', ['_value' => '<b>Alice</b>'])
            ->willReturn('&lt;b&gt;Alice&lt;/b&gt;');

        $preset = $this->makePreset('name', null);

        $renderedPreset = $this->presetRenderer->render($preset, ['name' => '<b>Alice</b>']);

        self::assertSame('&lt;b&gt;Alice&lt;/b&gt;', $renderedPreset->getContent());
    }

    public function testRenderDoesNotEscapeValueProducedByTemplate(): void
    {
        $fileTemplate = new FileTemplate('foo.html.twig');

        $this->templateRenderer
            ->expects(self::once())
            ->method('render')
            ->willReturn('<b>Alice</b>');

        $this->templateRenderer
            ->expects(self::never())
            ->method('renderFromString');

        $preset = $this->makePreset('name', $fileTemplate);

        $renderedPreset = $this->presetRenderer->render($preset, ['name' => 'Alice']);

        self::assertSame('<b>Alice</b>', $renderedPreset->getContent());
    }

    public function testRenderCastsStringableValue(): void
    {
        $item = new class {
            public function __toString(): string
            {
                return 'Alice';
            }
        };

        $this->templateRenderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('{{ _value }}', ['_value' => 'Alice'])
            ->willReturn('Alice');

        $preset = $this->makePreset(null, null);

        $renderedPreset = $this->presetRenderer->render($preset, $item);

        self::assertSame('Alice', $renderedPreset->getContent());
    }

    public function testRenderReturnsEmptyStringForNullValue(): void
    {
        $this->templateRenderer
            ->expects(self::never())
            ->method('renderFromString');

        $preset = $this->makePreset('name', null);

        $renderedPreset = $this->presetRenderer->render($preset, ['name' => null]);

        self::assertSame('', $renderedPreset->getContent());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testRenderWrapsTemplateFailure(): void
    {
        $this->templateRenderer
            ->method('render')
            ->willThrowException(new RuntimeException('render failed'));

        $preset = $this->makePreset('name', new FileTemplate('foo.html.twig'));

        $this->expectException(TemplateRenderingException::class);

        $this->presetRenderer->render($preset, ['name' => 'Alice']);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testUnexpectedContentValueTypeExceptionCarriesContext(): void
    {
        $preset = $this->makePreset(null, null);
        $item   = ['name' => 'Alice'];

        try {
            $this->presetRenderer->render($preset, $item);

            self::fail('Expected ' . UnexpectedContentValueTypeException::class . ' to be thrown.');
        } catch (UnexpectedContentValueTypeException $e) {
            self::assertSame($preset, $e->getPreset());
            self::assertSame($item, $e->getItem());
            self::assertNull($e->getSource());
            self::assertSame($item, $e->getValue());
        }
    }

    public function testRenderPassesEnumItemAsValue(): void
    {
        $fileTemplate = new FileTemplate('foo.html.twig');

        $this->templateRenderer
            ->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo($fileTemplate),
                [
                    '_item'  => StatusEnum::Alive,
                    '_value' => StatusEnum::Alive,
                ],
            )
            ->willReturn('<span>Alive</span>');

        $preset = $this->makePreset(null, $fileTemplate);

        $renderedPreset = $this->presetRenderer->render($preset, StatusEnum::Alive);

        self::assertSame('<span>Alive</span>', $renderedPreset->getContent());
    }

    public function testRenderPassesEnumItemAsValueDespitePresetSource(): void
    {
        $fileTemplate = new FileTemplate('foo.html.twig');

        $this->templateRenderer
            ->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo($fileTemplate),
                [
                    '_item'  => StatusEnum::Alive,
                    '_value' => StatusEnum::Alive,
                ],
            )
            ->willReturn('<span>Alive</span>');

        $preset = $this->makePreset('lifeStatus', $fileTemplate);

        $renderedPreset = $this->presetRenderer->render($preset, StatusEnum::Alive);

        self::assertSame('<span>Alive</span>', $renderedPreset->getContent());
    }

    public function testRenderWithoutTemplateUsesBackedEnumValue(): void
    {
        $this->templateRenderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('{{ _value }}', ['_value' => 'ALIVE'])
            ->willReturn('ALIVE');

        $preset = $this->makePreset(null, null);

        $renderedPreset = $this->presetRenderer->render($preset, StatusEnum::Alive);

        self::assertSame('ALIVE', $renderedPreset->getContent());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testRenderWithoutTemplateThrowsOnPureEnum(): void
    {
        $preset = $this->makePreset(null, null);

        $this->expectException(UnexpectedContentValueTypeException::class);

        $this->presetRenderer->render($preset, PureEnum::Alive);
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
