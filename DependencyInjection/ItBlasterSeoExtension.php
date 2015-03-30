<?php

namespace ItBlaster\SeoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ItBlasterSeoExtension extends Extension
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

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('services_sonata_admin.yml');
        }

        $container->setParameter('it_blaster_seo.edit_mode.roles', $config['edit_mode']['roles']);

        if (isset($config['admin']['seo_param']['class'])) {
            $container->setParameter('it_blaster_seo.admin.seo_param.class', $config['admin']['seo_param']['class']);
        }

    }

}
