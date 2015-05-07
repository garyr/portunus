<?php

namespace Portunus\Test\Model;

use Portunus\Model\Safe;

class SafeTest extends ModelUnitTestCase
{
    public function testPersist()
    {
        $safe = new Safe();
        $keyPair = $safe->generateKeyPair();
        $safe->setName('TestSafeName');
        $safe->setPublicKey($keyPair->getPublicKey());
        $safe->setCreated();
        $safe->setUpdated();

        parent::persist($safe, array('name' => 'TestSafeName'));
    }

    public function testDelete()
    {
        $safe = new Safe();
        $keyPair = $safe->generateKeyPair();
        $safe->setName(__CLASS__ . __METHOD__);
        $safe->setPublicKey($keyPair->getPublicKey());
        $safe->setCreated();
        $safe->setUpdated();

        parent::remove($safe, array('name' => __CLASS__ . __METHOD__));
    }
}