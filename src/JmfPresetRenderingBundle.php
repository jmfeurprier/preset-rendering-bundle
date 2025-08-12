<?php

declare(strict_types=1);

namespace Jmf\PresetRendering;

use Jmf\PresetRendering\Preset\CacheablePresetCollectionLoader;
use Jmf\PresetRendering\Preset\PresetCollectionLoader;
use Jmf\PresetRendering\Preset\PresetCollectionLoaderInterface;
use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class JmfPresetRenderingBundle extends AbstractBundle
{
    /**
     * @const array<non-empty-string, non-empty-string>
     */
    private const array PARAMETERS_MAPPING = [
        'presets'               => 'preset_configurations',
        'properties'            => 'property_configurations',
        'twig_functions_prefix' => 'twig_functions_prefix',
    ];

    protected string $extensionAlias = 'jmf_preset_rendering';

    #[Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    #[Override]
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $container->import('../config/services.yaml');

        $this->loadParameters($config, $container);

        $container->services()
            ->get(CacheablePresetCollectionLoader::class)
            ->arg('$wrapped', new Reference(PresetCollectionLoader::class))
        ;

        $container->services()
            ->alias(PresetCollectionLoaderInterface::class, CacheablePresetCollectionLoader::class)
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
