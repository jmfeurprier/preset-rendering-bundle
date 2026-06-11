<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset;

use Jmf\RenderingPreset\Configuration\PresetConfigurationFileLoader;
use Jmf\RenderingPreset\Exception\DuplicatePresetException;
use Jmf\RenderingPreset\Exception\DuplicatePropertyException;
use Jmf\RenderingPreset\Exception\InvalidPresetFileException;
use Jmf\RenderingPreset\Exception\ReservedPropertyKeyException;
use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
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

    public function __construct(
        private readonly PresetConfigurationFileLoader $presetConfigurationFileLoader = new PresetConfigurationFileLoader(),
    ) {
    }

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
     *
     * @throws DuplicatePresetException
     * @throws DuplicatePropertyException
     * @throws InvalidPresetFileException
     * @throws ReservedPropertyKeyException
     */
    #[Override]
    public function loadExtension(
        array $config,
        ContainerConfigurator $configurator,
        ContainerBuilder $container,
    ): void {
        $configurator->import('../config/services.yaml');

        $config['presets'] = $this->presetConfigurationFileLoader->load(
            $config,
            $container,
            $this->extensionAlias,
        );

        $this->loadParameters($config, $configurator);
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
