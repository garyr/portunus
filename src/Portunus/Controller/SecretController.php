<?php

namespace Portunus\Controller;

use Portunus\ContainerAwareTrait;
use Portunus\Model\Safe;
use Portunus\Model\Secret;

class SecretController
{
    use ContainerAwareTrait;

    public function create(Safe $safe, $key, $value)
    {
        $repository = $this->getEntityManager()->getRepository('Portunus\Model\Secret');
        $secret = $repository->findOneBy(array('safe' => $safe, 'key' => $key));

        if (!$secret) {
            $secret = new Secret();
            $secret->setCreated();
        }

        $safeRef = $this->getEntityManager()->getReference('Portunus\Model\Safe', $safe->getId());
        $secret->setSafe($safeRef);
        $secret->setKey($key);
        $secret->setValue($safe->getPublicKey(), $value);
        $secret->setUpdated();
        $this->getEntityManager()->persist($secret);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Safe $safe
     * @param $key
     * @return bool
     */
    public function remove(Safe $safe, $key)
    {
        $repository = $this->getEntityManager()->getRepository('Portunus\Model\Secret');
        $secret = $repository->findOneBy(array('safe' => $safe, 'key' => $key));

        if (!$secret) {
            return false;
        }

        $this->getEntityManager()->remove($secret);
        $this->getEntityManager()->flush();

        $secret = $repository->findOneBy(array('safe' => $safe, 'key' => $key));

        if ($secret) {
            return false;
        }

        return true;
    }

    /**
     * @param Safe $safe
     * @param $name
     * @return Secret
     */
    public function view(Safe $safe, $name)
    {
        $repository = $this->getEntityManager()->getRepository('Portunus\Model\Secret');

        return $repository->findOneBy(array('safe' => $safe, 'key' => $name));
    }

    public function listSecrets(Safe $safe)
    {
        $repository = $this->getEntityManager()->getRepository('Portunus\Model\Secret');
        $secretCollection = $repository->findBy(array('safe' => $safe));

        return $secretCollection;
    }

    /**
     * @param Safe $safe
     * @return array
     */
    public function getKeys(Safe $safe)
    {
        $secretKeyCollection = $this->listSecrets($safe);

        $keyNames = array();
        foreach ($secretKeyCollection as $key => $secret) {
            $keyNames[] = $secret->getKey();
        }

        return $keyNames;
    }
}