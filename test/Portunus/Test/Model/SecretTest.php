<?php

namespace Portunus\Test\Model;

use Portunus\Model\Safe;
use Portunus\Model\Secret;

class SecretTest extends ModelUnitTestCase
{

    public function testPersist()
    {
        $safe = new Safe();
        $keyPair = $safe->generateKeyPair();
        $safe->setName('TestSafeName');
        $safe->setPublicKey($keyPair->getPublicKey());
        $safe->setCreated();
        $safe->setUpdated();
        $this->getEntityManager()->persist($safe);
        $this->getEntityManager()->flush();

        $secret = new Secret();
        $secret->setCreated();
        $secret->setUpdated();
        $secret->setKey('foo');

        $value = 'bar';
        $secret->setValue($keyPair->getPublicKey(), $value);
        $this->assertNotEquals($secret->getValue(), $value);
        $this->assertEquals($secret->getValue($keyPair->getPrivateKey()), $value);

        $safeRef = $this->getEntityManager()->getReference(get_class($safe), $safe->getId());
        $secret->setSafe($safeRef);

        parent::persist($secret, array('key' => 'foo'));
    }

    /**
     * @expectedException \Exception
     */
    public function testDuplicate()
    {
        $safe = new Safe();
        $keyPair = $safe->generateKeyPair();
        $safe->setName('TestSafeName');
        $safe->setPublicKey($keyPair->getPublicKey());
        $safe->setCreated();
        $safe->setUpdated();
        $this->getEntityManager()->persist($safe);
        $this->getEntityManager()->flush();

        $secret = new Secret();
        $secret->setKey(__CLASS__ . __METHOD__);

        $value = __LINE__;
        $secret->setValue($keyPair->getPublicKey(), $value);
        $this->assertNotEquals($secret->getValue(), $value);
        $this->assertEquals($secret->getValue($keyPair->getPrivateKey()), $value);

        $safeRef = $this->getEntityManager()->getReference(get_class($safe), $safe->getId());
        $secret->setSafe($safeRef);

        $this->getEntityManager()->persist($secret);
        $this->getEntityManager()->flush();

        $secret = new Secret();
        $secret->setCreated();
        $secret->setUpdated();
        $secret->setKey(__CLASS__ . __METHOD__);

        $value = __LINE__;
        $secret->setValue($keyPair->getPublicKey(), $value);
        $this->assertNotEquals($secret->getValue(), $value);
        $this->assertEquals($secret->getValue($keyPair->getPrivateKey()), $value);

        $safeRef = $this->getEntityManager()->getReference(get_class($safe), $safe->getId());
        $secret->setSafe($safeRef);

        $this->getEntityManager()->persist($secret);
        $this->getEntityManager()->flush();
    }

    public function testDelete()
    {
        $safe = new Safe();
        $keyPair = $safe->generateKeyPair();
        $safe->setName('TestSafeName');
        $safe->setPublicKey($keyPair->getPublicKey());
        $safe->setCreated();
        $safe->setUpdated();
        $this->getEntityManager()->persist($safe);
        $this->getEntityManager()->flush();


        $secret = new Secret();
        $secret->setCreated();
        $secret->setUpdated();
        $secret->setKey(__CLASS__ . __METHOD__);

        $value = __LINE__;
        $secret->setValue($keyPair->getPublicKey(), $value);
        $this->assertNotEquals($secret->getValue(), $value);
        $this->assertEquals($secret->getValue($keyPair->getPrivateKey()), $value);

        $safeRef = $this->getEntityManager()->getReference(get_class($safe), $safe->getId());
        $secret->setSafe($safeRef);

        parent::remove($secret, array('key' => __CLASS__ . __METHOD__));
    }

    public function testLongSecret()
    {
        $safe = new Safe();
        $keyPair = $safe->generateKeyPair();
        $safe->setName('TestSafeName');
        $safe->setPublicKey($keyPair->getPublicKey());
        $safe->setCreated();
        $safe->setUpdated();
        $this->getEntityManager()->persist($safe);
        $this->getEntityManager()->flush();

        $secret = new Secret();
        $secret->setCreated();
        $secret->setUpdated();
        $secret->setKey(__CLASS__ . __METHOD__);

        // generate enough text to require chunked data
        $value = str_repeat('a', ($keyPair->getPublicKey()->getKeySize() / 8) * 2);

        $secret->setValue($keyPair->getPublicKey(), $value);
        $this->assertNotEquals($secret->getValue(), $value);
        $this->assertEquals($secret->getValue($keyPair->getPrivateKey()), $value);

        $safeRef = $this->getEntityManager()->getReference(get_class($safe), $safe->getId());
        $secret->setSafe($safeRef);

        parent::remove($secret, array('key' => __CLASS__ . __METHOD__));
    }
}