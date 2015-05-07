<?php

namespace Portunus\Crypt\RSA;

use Portunus\Crypt\Key;

class PrivateKey extends Key
{
    use KeyPairTrait;

    protected function getKeyDetails()
    {
        $res = openssl_pkey_get_private($this->getKey());
        return openssl_pkey_get_details($res);
    }

    public function decrypt($cipherText)
    {
        $plainText = '';
        openssl_private_decrypt($cipherText, $plainText, $this->getKey());

        return $plainText;
    }
}