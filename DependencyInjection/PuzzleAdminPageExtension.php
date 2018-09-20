<?php

namespace Puzzle\Admin\PageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PuzzleAdminPageExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        $container->setParameter('admin_page', $config);
        $container->setParameter('admin_page.title', $config['title']);
        $container->setParameter('admin_page.description', $config['description']);
        $container->setParameter('admin_page.icon', $config['icon']);
        $container->setParameter('admin_page.roles', $config['roles']);
        $container->setParameter('admin_page.dirname', $config['dirname']);
    }
}
