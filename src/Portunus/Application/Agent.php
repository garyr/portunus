<?php

namespace Portunus\Application;

use Portunus\Controller\SafeController;
use Portunus\Controller\SecretController;
use Portunus\Crypt\RSA\PrivateKey;
use Portunus\Model\Safe;
use Portunus\ContainerAwareTrait;

class Agent
{
    use ContainerAwareTrait;

    /**
     * @var Safe|string
     */
    protected $safe;

    /**
     * @var callable
     */
    protected $privateKeyCallback;

    /**
     * @param Safe|string $safe
     * @param callable $privateKeyCallback
     */
    public function __construct($safe = null, callable $privateKeyCallback = null)
    {
        $this->setSafe($safe);
        $this->setPrivateKeyCallback($privateKeyCallback);
    }

    /**
     * @param $keyName
     * @return bool|string
     * @throws \Exception
     */
    public function getKey($keyName)
    {
        if (!is_callable($this->privateKeyCallback)) {
            throw new \Exception("Missing private key callback");
        }

        $SafeController = new SafeController();
        $SafeController->setContainer($this->getContainer());
        $SecretController = new SecretController();
        $SecretController->setContainer($this->getContainer());

        $safe = $this->getSafe();
        if (is_string($safe)) {
            $safe = $SafeController->view($this->getSafe());
            if (!$safe) {
                throw new \Exception("Invalid Portunus safe");
            }
        }

        $secret = $SecretController->view($safe, $keyName);

        $callback = $this->privateKeyCallback;
        $privateKeyString = $callback($safe->getName());

        if (empty($privateKeyString)) {
            throw new \Exception("Invalid private key");
        }

        $PrivateKey = new PrivateKey();
        $PrivateKey->setKey($privateKeyString);

        try {
            $result = $secret->getValue($PrivateKey);
        } catch(\Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * @return Safe|string
     */
    public function getSafe()
    {
        return $this->safe;
    }

    /**
     * @param Safe|string $safe
     */
    public function setSafe($safe)
    {
        $this->safe = $safe;
    }

    protected function getPrivateKeyCallback()
    {
        return $this->privateKeyCallback;
    }


    /**
     * @param callable $privateKeyCallback
     */
    public function setPrivateKeyCallback(callable $privateKeyCallback = null)
    {
        $this->privateKeyCallback = $privateKeyCallback;
    }
}