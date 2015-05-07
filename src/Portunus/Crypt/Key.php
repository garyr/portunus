<?php

namespace Portunus\Crypt;

class Key
{
    private $key;

    public function __construct($key = null)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getKeySignature($algo = 'sha256')
    {
        return hash($algo, $this->getKey());
    }

    public function setKey($key)
    {
        $this->key = $key;
    }
}
