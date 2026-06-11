<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Configuration;

use Jmf\RenderingPreset\Exception\DuplicatePresetException;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

/**
 * Owns the `presets` side of the bundle configuration: assembles the final map from the per-preset
 * files discovered under the configured `paths` (filename = preset id) merged with the inline
 * `presets`, and registers a cache-invalidation resource per directory. A preset id defined more
 * than once (across files, or against an inline entry) is a configuration error.
 */
final readonly class PresetConfigurationFileLoader
{
    /**
     * @param array<string, mixed> $config         resolved `jmf_rendering_preset` config (`paths` + `presets`)
     * @param string               $extensionAlias used to derive the default path
     *
     * @return array<mixed, mixed> preset configs keyed by id
     *
     * @throws DuplicatePresetException
     */
    public function load(
        array $config,
        ContainerBuilder $container,
        string $extensionAlias,
    ): array {
        $directories = $this->resolveDirectories($config, $container, $extensionAlias);

        $this->registerResources($directories, $container);

        $presetsFromPaths = $this->loadFromPaths($directories);

        $inlinePresets = $config['presets'];
        Assert::isArray($inlinePresets);

        $duplicates = array_intersect_key($presetsFromPaths, $inlinePresets);

        if ([] !== $duplicates) {
            throw new DuplicatePresetException(array_keys($duplicates));
        }

        return $presetsFromPaths + $inlinePresets;
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return list<string> resolved (absolute) directories
     */
    private function resolveDirectories(
        array $config,
        ContainerBuilder $container,
        string $extensionAlias,
    ): array {
        $paths = $config['paths'];
        Assert::isArray($paths);

        if ([] === $paths) {
            // Default: <config-dir>/packages/<extension alias>, e.g. config/packages/jmf_rendering_preset.
            // `.kernel.config_dir` is the build-time materialization of Kernel::getConfigDir(), the
            // only handle a bundle extension has to it; the alias keeps the segment rename-safe.
            $paths = ['%.kernel.config_dir%/packages/' . $extensionAlias];
        }

        $directories = [];

        foreach ($paths as $path) {
            Assert::string($path);

            $directory = $container->getParameterBag()->resolveValue($path);
            Assert::string($directory);

            $directories[] = $directory;
        }

        return $directories;
    }

    /**
     * @param list<string> $directories
     */
    private function registerResources(
        array $directories,
        ContainerBuilder $container,
    ): void {
        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                // DirectoryResource is mtime-based + recursive, so the compiled container is
                // rebuilt when a file in the directory is added, removed or edited.
                $container->addResource(new DirectoryResource($directory, '/\.yaml$/'));
            }
        }
    }

    /**
     * @param list<string> $directories
     *
     * @return array<string, array<mixed, mixed>>
     *
     * @throws DuplicatePresetException
     */
    private function loadFromPaths(
        array $directories,
    ): array {
        $presets = [];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            foreach ((new Finder())->files()->in($directory)->name('*.yaml')->sortByName() as $file) {
                $presetId = substr($file->getRelativePathname(), 0, -strlen('.yaml'));

                if (isset($presets[$presetId])) {
                    throw new DuplicatePresetException([$presetId]);
                }

                // PARSE_CONSTANT so files may use `!php/const ...`, matching what Symfony's own
                // config loader enables for inline config.
                $parsed = Yaml::parseFile($file->getRealPath(), Yaml::PARSE_CONSTANT);

                $presets[$presetId] = is_array($parsed) ? $parsed : [];
            }
        }

        return $presets;
    }
}