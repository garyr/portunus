<?php

namespace Portunus;

use Symfony\Component\DependencyInjection;
use Symfony\Component\Config\FileLocator;

class ContainerBuilder extends DependencyInjection\ContainerBuilder
{

    public function __construct()
    {
        parent::__construct();
        $fileLocator = new FileLocator(__DIR__ . '/../../config/');
        $loader = new DependencyInjection\Loader\XmlFileLoader($this, $fileLocator);
        $loader->load('services.xml');
    }
}