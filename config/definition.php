<?php

declare(strict_types=1);

use Jmf\RenderingPreset\Twig\RenderingPresetExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->fixXmlConfig('preset')
        ->fixXmlConfig('property')
        ->children()
            ->arrayNode('presets')
                ->defaultValue([])
                ->arrayPrototype()
                    ->ignoreExtraKeys(false)
                    ->children()
                        ->stringNode('parent')->defaultNull()->cannotBeEmpty()->end()
                        ->stringNode('source')->defaultNull()->cannotBeEmpty()->end()
                        ->stringNode('template')->defaultNull()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('properties')
                ->arrayPrototype()
                    ->fixXmlConfig('choice')
                    ->children()
                        ->arrayNode('choices')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->variableNode('default')->defaultNull()->end()
                        ->booleanNode('required')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
            ->stringNode('twig_functions_prefix')
                ->info('Twig functions prefix.')
                ->defaultValue(RenderingPresetExtension::PREFIX_DEFAULT)
            ->end()
        ->end()
    ;
};
