<?php

namespace Engage360d\Bundle\TakedaTestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class Engage360dTakedaTestExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services/services.yml');
        
        $container->getDefinition('engage360d.takeda_test.processing.recommendations_mather_factory')
            ->addArgument(__DIR__ . '/../Resources/recommendations.yml');
    }
}
