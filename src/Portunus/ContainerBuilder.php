<?php

namespace Portunus;

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

class ContainerBuilder extends DependencyInjection\ContainerBuilder
{

    public function __construct()
    {
        parent::__construct();
        $fileLocator = new FileLocator(__DIR__ . '/../../config/');

        $resolver = new LoaderResolver(array(
            new XmlFileLoader($this, $fileLocator),
            new YamlFileLoader($this, $fileLocator),
        ));

        $loader = new DelegatingLoader($resolver);
        $loader->load('services.yml');
    }
}
