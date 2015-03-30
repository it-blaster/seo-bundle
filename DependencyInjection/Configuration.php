<?php

namespace ItBlaster\SeoBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('it_blaster_seo');

        $rootNode->children()
            ->arrayNode('admin')
                ->children()
                    ->arrayNode('seo_param')
                        ->children()
                            ->scalarNode('class')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()

            ->arrayNode('edit_mode')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('roles')
                            ->prototype('scalar')->end()
                            ->cannotBeEmpty()
                            ->defaultValue(array('ROLE_SUPER_ADMIN'))
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
