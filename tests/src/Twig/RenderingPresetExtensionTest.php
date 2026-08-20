<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Twig;

use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\RenderingPreset\Preset\Rendering\PresetRendererInterface;
use Jmf\RenderingPreset\Preset\Rendering\RenderedPreset;
use Jmf\RenderingPreset\Twig\RenderingPresetExtension;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Twig\Node\Node;
use Twig\TwigFunction;

final class RenderingPresetExtensionTest extends TestCase
{
    private PresetRepositoryInterface & Stub $presetRepository;

    private PresetRendererInterface & MockObject $presetRenderer;

    private Preset $preset;

    private RenderedPreset $renderedPreset;

    #[Override]
    protected function setUp(): void
    {
        $this->presetRepository = self::createStub(PresetRepositoryInterface::class);
        $this->presetRenderer   = $this->createMock(PresetRendererInterface::class);

        $this->preset = new Preset(
            id:         'price',
            source:     null,
            template:   null,
            properties: new PresetPropertyCollection([]),
        );

        $this->renderedPreset = new RenderedPreset(
            content:    '12.50',
            properties: new PresetPropertyCollection([]),
        );

        $this->presetRepository
            ->method('get')
            ->willReturn($this->preset);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetFunctionNames(): void
    {
        $renderingPresetExtension = $this->makeExtension();

        self::assertSame(
            ['preset_render', 'preset_get'],
            array_map(
                static fn (TwigFunction $function): string => $function->getName(),
                $renderingPresetExtension->getFunctions(),
            ),
        );
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testGetFunctionNamesWithPrefix(): void
    {
        $renderingPresetExtension = $this->makeExtension('jmf_');

        self::assertSame(
            ['jmf_preset_render', 'jmf_preset_get'],
            array_map(
                static fn (TwigFunction $function): string => $function->getName(),
                $renderingPresetExtension->getFunctions(),
            ),
        );
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testFunctionsAreHtmlSafe(): void
    {
        $renderingPresetExtension = $this->makeExtension();

        foreach ($renderingPresetExtension->getFunctions() as $twigFunction) {
            self::assertSame(['html'], $twigFunction->getSafe(new Node()));
        }
    }

    public function testRenderReturnsContent(): void
    {
        $this->presetRenderer
            ->expects(self::once())
            ->method('render')
            ->with(self::identicalTo($this->preset), ['price' => 12.5], null)
            ->willReturn($this->renderedPreset);

        $renderingPresetExtension = $this->makeExtension();

        self::assertSame('12.50', $renderingPresetExtension->render('price', ['price' => 12.5]));
    }

    public function testRenderPassesSourceThrough(): void
    {
        $this->presetRenderer
            ->expects(self::once())
            ->method('render')
            ->with(self::identicalTo($this->preset), ['amount' => 12.5], 'amount')
            ->willReturn($this->renderedPreset);

        $renderingPresetExtension = $this->makeExtension();

        self::assertSame('12.50', $renderingPresetExtension->render('price', ['amount' => 12.5], 'amount'));
    }

    public function testGetReturnsRenderedPreset(): void
    {
        $this->presetRenderer
            ->expects(self::once())
            ->method('render')
            ->willReturn($this->renderedPreset);

        $renderingPresetExtension = $this->makeExtension();

        self::assertSame($this->renderedPreset, $renderingPresetExtension->get('price', ['price' => 12.5]));
    }

    #[DataProvider('provideItems')]
    public function testItemIsPassedThroughUnchanged(mixed $item): void
    {
        $this->presetRenderer
            ->expects(self::once())
            ->method('render')
            ->with(self::identicalTo($this->preset), $item, null)
            ->willReturn($this->renderedPreset);

        $renderingPresetExtension = $this->makeExtension();

        self::assertSame('12.50', $renderingPresetExtension->render('price', $item));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideItems(): array
    {
        return [
            'array'  => [['price' => 12.5]],
            'object' => [new \stdClass()],
            'string' => ['12.50'],
            'float'  => [12.5],
            'bool'   => [true],
            'null'   => [null],
        ];
    }

    private function makeExtension(
        string $prefix = RenderingPresetExtension::PREFIX_DEFAULT,
    ): RenderingPresetExtension {
        return new RenderingPresetExtension(
            $this->presetRepository,
            $this->presetRenderer,
            $prefix,
        );
    }
}
