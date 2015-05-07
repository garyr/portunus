<?php

namespace Portunus;

use Symfony\Component\DependencyInjection;
use Doctrine\ORM\EntityManager;

trait ContainerAwareTrait
{
    use DependencyInjection\ContainerAwareTrait;

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        if (!$this->container) {
            $this->container = new ContainerBuilder();
        }

        return $this->container;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.entity_manager');
    }
}
