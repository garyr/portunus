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
        $resource = openssl_pkey_new(array('private_key_bits' => $this->keySize));
        $pubKey = openssl_pkey_get_details($resource);
        $private = '';
        openssl_pkey_export($resource, $private);
        $this->setPrivateKey(new PrivateKey($private));
        $this->setPublicKey(new PublicKey($pubKey['key']));
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