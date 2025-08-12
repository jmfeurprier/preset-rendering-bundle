<?php

declare(strict_types=1);

use Jmf\PresetRendering\Twig\PresetRenderingExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->fixXmlConfig('preset')
        ->fixXmlConfig('property')
        ->children()
            ->arrayNode('presets')
                ->defaultValue([])
                ->arrayPrototype()
                    ->ignoreExtraKeys()
                    ->children()
                        ->stringNode('label')->defaultNull()->end()
                        ->stringNode('parent')->defaultNull()->cannotBeEmpty()->end()
                        ->stringNode('source')->defaultNull()->cannotBeEmpty()->end()
                        ->variableNode('template')->defaultNull()->end()
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
                ->defaultValue(PresetRenderingExtension::PREFIX_DEFAULT)
            ->end()
        ->end()
    ;
};
