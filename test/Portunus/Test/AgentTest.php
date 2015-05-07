<?php

namespace Portunus\Test;

use Portunus\Test\Model\ModelUnitTestCase;

use Portunus\Agent;
use Portunus\Model\Safe;
use Portunus\Model\Secret;

class AgentTest extends ModelUnitTestCase
{
    public function testAgent()
    {
        $safe = new Safe();
        $safe->setCreated();
        $safe->setUpdated();
        $keyPair = $safe->generateKeyPair();
        $safe->setName('TestSafeName');
        $safe->setPublicKey($keyPair->getPublicKey());
        $this->getEntityManager()->persist($safe);
        $this->getEntityManager()->flush();

        $secret = new Secret();
        $secret->setCreated();
        $secret->setUpdated();

        $keyName = 'foo';
        $value = 'bar';

        $secret->setKey($keyName);

        $secret->setValue($keyPair->getPublicKey(), $value);

        $safeRef = $this->getEntityManager()->getReference(get_class($safe), $safe->getId());
        $secret->setSafe($safeRef);

        $this->getEntityManager()->persist($secret);
        $this->getEntityManager()->flush();

        $callback = function($safeName) use ($keyPair) {
            return $keyPair->getPrivateKey()->getKey();
        };

        $Agent = new Agent();
        $Agent->setContainer($this->getContainer());
        $Agent->setSafe($safe);
        $Agent->setPrivateKeyCallback($callback);

        $this->assertEquals($value, $Agent->getKey($keyName));
    }
}