<?php

namespace Portunus\Crypt\RSA;

use Portunus\Crypt\Key;

class PublicKey extends Key
{
    use KeyPairTrait;

    protected function getKeyDetails()
    {
        $res = openssl_pkey_get_public($this->getKey());
        return openssl_pkey_get_details($res);
    }

    public function encrypt($plainText)
    {
        $cipherText = '';
        openssl_public_encrypt($plainText, $cipherText, $this->getKey());

        return $cipherText;
    }
}
