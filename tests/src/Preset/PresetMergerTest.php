<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset;

use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\PresetMerger;
use Jmf\RenderingPreset\Preset\Property\PresetProperty;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollectionMerger;
use Jmf\TemplateRendering\FileTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Override;
use PHPUnit\Framework\TestCase;

final class PresetMergerTest extends TestCase
{
    private PresetMerger $presetMerger;

    #[Override]
    protected function setUp(): void
    {
        $this->presetMerger = new PresetMerger(new PresetPropertyCollectionMerger());
    }

    public function testMergeIdAlwaysFromChild(): void
    {
        $preset  = $this->makePreset('child', null, null, []);
        $parent = $this->makePreset('parent', null, null, []);

        $result = $this->presetMerger->merge($preset, $parent);

        self::assertSame('child', $result->getId());
    }

    public function testMergeUsesChildSource(): void
    {
        $preset  = $this->makePreset('child', 'childSource', null, []);
        $parent = $this->makePreset('parent', 'parentSource', null, []);

        $result = $this->presetMerger->merge($preset, $parent);

        self::assertSame('childSource', $result->getSource());
    }

    public function testMergeInheritsParentSource(): void
    {
        $preset  = $this->makePreset('child', null, null, []);
        $parent = $this->makePreset('parent', 'parentSource', null, []);

        $result = $this->presetMerger->merge($preset, $parent);

        self::assertSame('parentSource', $result->getSource());
    }

    public function testMergeUsesChildTemplate(): void
    {
        $childTemplate  = new FileTemplate('child.html.twig');
        $parentTemplate = new FileTemplate('parent.html.twig');

        $preset  = $this->makePreset('child', null, $childTemplate, []);
        $parent = $this->makePreset('parent', null, $parentTemplate, []);

        $result = $this->presetMerger->merge($preset, $parent);

        self::assertSame($childTemplate, $result->getTemplate());
    }

    public function testMergeInheritsParentTemplate(): void
    {
        $fileTemplate = new FileTemplate('parent.html.twig');

        $preset  = $this->makePreset('child', null, null, []);
        $parent = $this->makePreset('parent', null, $fileTemplate, []);

        $result = $this->presetMerger->merge($preset, $parent);

        self::assertSame($fileTemplate, $result->getTemplate());
    }

    public function testMergePropertiesChildOverridesParent(): void
    {
        $preset  = $this->makePreset('child', null, null, ['color' => 'blue']);
        $parent = $this->makePreset('parent', null, null, ['color' => 'red']);

        $result = $this->presetMerger->merge($preset, $parent);

        self::assertSame('blue', $result->getProperties()->getValue('color'));
    }

    /**
     * @param non-empty-string               $id
     * @param null|non-empty-string          $source
     * @param array<non-empty-string, mixed> $properties
     */
    private function makePreset(
        string $id,
        ?string $source,
        ?TemplateInterface $template,
        array $properties,
    ): Preset {
        $presetProperties = [];

        foreach ($properties as $key => $value) {
            $presetProperties[] = new PresetProperty($key, $value);
        }

        return new Preset(
            id:         $id,
            source:     $source,
            template:   $template,
            properties: new PresetPropertyCollection($presetProperties),
        );
    }
}
