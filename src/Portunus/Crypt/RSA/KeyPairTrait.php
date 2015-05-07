<?php

namespace Portunus\Crypt\RSA;

trait KeyPairTrait
{

    public function getKeySize()
    {
        return $this->getKeyDetail('bits');
    }

    protected function getKeyDetail($detail)
    {
        $detailValue = null;
        $details = $this->getKeyDetails();

        if (is_array($details) && array_key_exists($detail, $details)) {
            $detailValue = $details[$detail];
        }

        return $detailValue;
    }
}