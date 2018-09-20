<?php

namespace Puzzle\Admin\PageBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('puzzle_admin_page');
        
        $rootNode
            ->children()
                ->scalarNode('title')->defaultValue('page.title')->end()
                ->scalarNode('description')->defaultValue('page.description')->end()
                ->scalarNode('icon')->defaultValue('page.icon')->end()
                ->arrayNode('roles')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('label')->defaultValue('ROLE_PAGE')->end()
                                ->scalarNode('description')->defaultValue('page.role.default')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('dirname')->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
