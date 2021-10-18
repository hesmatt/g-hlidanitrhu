<?php

namespace Matt\SyGridBundle\DependencyInjection;

class SyGridExtension extends \Symfony\Component\DependencyInjection\Extension\Extension
{
    public function load(array $configs, \Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader(
            $container,
            new \Symfony\Component\Config\FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        $config = array();
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }

        if ($config !== []) {
            $cache = new \Matt\SyGridBundle\Grid\Cache\GridCaching();
            $cache->cacheConfiguration($config);
        }
    }

}