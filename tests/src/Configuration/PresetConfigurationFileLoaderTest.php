<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Configuration;

use Jmf\RenderingPreset\Configuration\PresetConfigurationFileLoader;
use Jmf\RenderingPreset\Exception\DuplicatePresetException;
use Jmf\RenderingPreset\Exception\InvalidPresetFileException;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PresetConfigurationFileLoaderTest extends TestCase
{
    private const string EXTENSION_ALIAS = 'jmf_rendering_preset';

    private ContainerBuilder $container;

    private PresetConfigurationFileLoader $presetConfigurationFileLoader;

    #[Override]
    protected function setUp(): void
    {
        $this->container                     = new ContainerBuilder();
        $this->presetConfigurationFileLoader = new PresetConfigurationFileLoader();
    }

    public function testLoadKeysPresetsByFilename(): void
    {
        $presets = $this->load([$this->fixturePath('presets')]);

        self::assertSame(['price', 'text'], array_keys($presets));
        self::assertSame(
            [
                'align'    => 'end',
                'label'    => 'Price',
                'source'   => 'price',
                'template' => 'preset/price.html.twig',
            ],
            $presets['price'],
        );
    }

    public function testLoadIgnoresNonYamlFilesAndSubDirectories(): void
    {
        $presets = $this->load([$this->fixturePath('presets')]);

        self::assertArrayNotHasKey('ignored', $presets);
    }

    public function testLoadMergesSeveralDirectories(): void
    {
        $presets = $this->load([$this->fixturePath('presets'), $this->fixturePath('other')]);

        self::assertSame(['price', 'text', 'date'], array_keys($presets));
    }

    public function testLoadIgnoresDuplicateDirectories(): void
    {
        $presets = $this->load([$this->fixturePath('presets'), $this->fixturePath('presets')]);

        self::assertSame(['price', 'text'], array_keys($presets));
    }

    public function testLoadMergesInlinePresets(): void
    {
        $presets = $this->load(
            [$this->fixturePath('presets')],
            ['inline' => ['label' => 'Inline']],
        );

        self::assertSame(['price', 'text', 'inline'], array_keys($presets));
        self::assertSame(['label' => 'Inline'], $presets['inline']);
    }

    public function testLoadWithMissingDirectoryAndNoInlinePresets(): void
    {
        $presets = $this->load([$this->fixturePath('missing')]);

        self::assertSame([], $presets);
    }

    public function testLoadThrowsOnPresetDefinedInFileAndInline(): void
    {
        $this->expectException(DuplicatePresetException::class);

        $this->load(
            [$this->fixturePath('presets')],
            ['price' => ['label' => 'Inline']],
        );
    }

    public function testLoadThrowsOnPresetDefinedInTwoDirectories(): void
    {
        $this->expectException(DuplicatePresetException::class);

        $this->load([$this->fixturePath('duplicate'), $this->fixturePath('presets')]);
    }

    public function testLoadThrowsOnFileWithoutMappingRoot(): void
    {
        $this->expectException(InvalidPresetFileException::class);

        $this->load([$this->fixturePath('invalid')]);
    }

    public function testLoadRegistersDirectoryResourceForExistingDirectory(): void
    {
        $directory = $this->fixturePath('presets');

        $this->load([$directory]);

        self::assertEquals(
            [new DirectoryResource($directory, '/\.yaml$/')],
            $this->container->getResources(),
        );
    }

    public function testLoadRegistersFileExistenceResourceForMissingDirectory(): void
    {
        $directory = $this->fixturePath('missing');

        $this->load([$directory]);

        self::assertEquals(
            [new FileExistenceResource($directory)],
            $this->container->getResources(),
        );
    }

    public function testLoadResolvesContainerParametersInPaths(): void
    {
        $this->container->setParameter('presets_dir', $this->fixturePath('presets'));

        $presets = $this->load(['%presets_dir%']);

        self::assertSame(['price', 'text'], array_keys($presets));
    }

    public function testLoadDefaultsToConfigDirPackagesExtensionAlias(): void
    {
        $this->container->setParameter('.kernel.config_dir', $this->fixturePath('config'));

        $presets = $this->load([]);

        self::assertSame(['from_default_path'], array_keys($presets));
    }

    /**
     * @param list<string>                        $paths
     * @param array<string, array<string, mixed>> $inlinePresets
     *
     * @return array<string, array<string, mixed>>
     *
     * @throws DuplicatePresetException
     * @throws InvalidPresetFileException
     */
    private function load(
        array $paths,
        array $inlinePresets = [],
    ): array {
        return $this->presetConfigurationFileLoader->load(
            [
                'paths'   => $paths,
                'presets' => $inlinePresets,
            ],
            $this->container,
            self::EXTENSION_ALIAS,
        );
    }

    private function fixturePath(string $name): string
    {
        return __DIR__ . '/fixtures/' . $name;
    }
}
