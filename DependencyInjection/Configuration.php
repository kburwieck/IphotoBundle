<?php

namespace Burwieck\IphotoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('burwieck_iphoto');

        $rootNode
            ->children()
                ->scalarNode('iphoto_path')->defaultValue('%kernel.root_dir%/../iPhoto Bibliothek')->end()
                ->scalarNode('target_path')->defaultValue('%kernel.root_dir%/../web/media/iPhoto')->end()
                ->arrayNode('import')
                    ->addDefaultsIfNotSet()
                    ->treatTrueLike(array('enabled' => true))
                    ->treatFalseLike(array('enabled' => false))
                    ->children()
                        ->arrayNode('keywords')
                            ->defaultValue(array())
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('faces')
                            ->defaultValue(array())
                            ->prototype('scalar')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
