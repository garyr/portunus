<?php

namespace Portunus\Controller;

use Portunus\ContainerAwareTrait;
use Portunus\Model\Safe;
use Portunus\Crypt\RSA\PrivateKey;

class SafeController
{
    use ContainerAwareTrait;

    /**
     * @param $name
     * @return PrivateKey
     */
    public function create($name)
    {
        $repository = $this->getEntityManager()->getRepository('Portunus\Model\Safe');
        $safe = $repository->findOneBy(array('name' => $name));

        if (!$safe) {
            $safe = new Safe();
            $safe->setCreated();
        }

        $keyPair = $safe->generateKeyPair();
        $safe->setName($name);
        $safe->setPublicKey($keyPair->getPublicKey());
        $safe->setUpdated();
        $this->getEntityManager()->persist($safe);
        $this->getEntityManager()->flush();

        return $keyPair->getPrivateKey();
    }

    /**
     * @param $name
     * @return bool
     */
    public function remove($name)
    {
        $repository = $this->getEntityManager()->getRepository('Portunus\Model\Safe');
        $safe = $repository->findOneBy(array('name' => $name));

        if (!$safe) {
            return false;
        }

        $this->getEntityManager()->remove($safe);
        $this->getEntityManager()->flush();

        $safe = $repository->findOneBy(array('name' => $name));

        if ($safe) {
            return false;
        }

        return true;
    }


    /**
     * @param $name
     * @return Safe
     */
    public function view($name)
    {
        $repository = $this->getEntityManager()->getRepository('Portunus\Model\Safe');

        return $repository->findOneBy(array('name' => $name));
    }

    /**
     * @return array
     */
    public function listSafes()
    {
        $repository = $this->getEntityManager()->getRepository('Portunus\Model\Safe');
        $safeCollection = $repository->findAll();

        return $safeCollection;
    }

    /**
     * @return array
     */
    public function getSafeNames()
    {
        $safeCollection = $this->listSafes();

        $safeNames = [];
        foreach ($safeCollection as $key => $safe) {
            $safeNames[] = $safe->getName();
        }

        return $safeNames;
    }
}