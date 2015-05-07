<?php

namespace Portunus\Crypt\RSA;

class KeyPair
{
    const DEFAULT_KEY_SIZE = 1024;

    const RSA_HEADER_LENGTH = 11;

    private $keySize = null;

    /**
     * @var PrivateKey
     */
    private $privateKey;

    /**
     * @var PublicKey
     */
    private $publicKey;

    public function generate($keySize = null)
    {
        $this->setKeySize($keySize);

        $rsa = new \Crypt_RSA();
        $key = $rsa->createKey($this->keySize);
        $this->setPrivateKey(new PrivateKey($key['privatekey']));
        $this->setPublicKey(new PublicKey($key['publickey']));
    }

    public function getKeySize()
    {
        return $this->keySize;
    }

    public function setKeySize($keySize)
    {
        if (!is_numeric($keySize)) {
            $keySize = self::DEFAULT_KEY_SIZE;
        }

        $this->keySize = $keySize;
    }

    /**
     * @return PrivateKey
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function setPrivateKey(PrivateKey $privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return PublicKey
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function setPublicKey(PublicKey $publicKey)
    {
        $this->publicKey = $publicKey;
    }

}