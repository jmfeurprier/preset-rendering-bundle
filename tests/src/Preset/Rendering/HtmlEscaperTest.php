<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset\Rendering;

use Jmf\RenderingPreset\Exception\HtmlEscapingException;
use Jmf\RenderingPreset\Preset\Rendering\HtmlEscaper;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class HtmlEscaperTest extends TestCase
{
    private TemplateRendererInterface & MockObject $templateRenderer;

    private HtmlEscaper $htmlEscaper;

    #[Override]
    protected function setUp(): void
    {
        $this->templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $this->htmlEscaper      = new HtmlEscaper($this->templateRenderer);
    }

    public function testEscape(): void
    {
        $this->templateRenderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('{{ _value }}', ['_value' => 'foo & bar'])
            ->willReturn('foo &amp; bar');

        $result = $this->htmlEscaper->escape('foo & bar');

        self::assertSame('foo &amp; bar', $result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testEscapeWrapsException(): void
    {
        $this->templateRenderer
            ->method('renderFromString')
            ->willThrowException(new RuntimeException('render failed'));

        $this->expectException(HtmlEscapingException::class);

        $this->htmlEscaper->escape('foo');
    }
}
