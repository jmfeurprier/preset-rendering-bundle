<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset;

use Jmf\RenderingPreset\Preset\CacheablePresetRepository;
use Jmf\RenderingPreset\Preset\PresetRepository;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class JmfRenderingPresetBundle extends AbstractBundle
{
    /**
     * @const array<non-empty-string, non-empty-string>
     */
    private const array PARAMETERS_MAPPING = [
        'presets'               => 'preset_configurations',
        'properties'            => 'property_configurations',
        'twig_functions_prefix' => 'twig_functions_prefix',
    ];

    protected string $extensionAlias = 'jmf_rendering_preset';

    #[Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    /**
     * @param array{
     *     presets: array<string, array{
     *         parent: string|null,
     *         source: string|null,
     *         template: string|null,
     *         ...
     *     }>,
     *     properties: array<string, array{
     *         choices: list<scalar>,
     *         default: mixed,
     *         required: bool,
     *     }>,
     *     twig_functions_prefix: string,
     * } $config
     */
    #[Override]
    public function loadExtension(
        array $config,
        ContainerConfigurator $configurator,
        ContainerBuilder $container,
    ): void {
        $configurator->import('../config/services.yaml');

        $this->loadParameters($config, $configurator);

        $configurator->services()
            ->get(CacheablePresetRepository::class)
            ->arg('$wrapped', new Reference(PresetRepository::class))
        ;

        $configurator->services()
            ->alias(PresetRepositoryInterface::class, CacheablePresetRepository::class)
        ;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadParameters(
        array $config,
        ContainerConfigurator $container,
    ): void {
        foreach (self::PARAMETERS_MAPPING as $configKey => $property) {
            $container->parameters()->set(
                "{$this->extensionAlias}.{$property}",
                $config[$configKey],
            );
        }
    }
}
