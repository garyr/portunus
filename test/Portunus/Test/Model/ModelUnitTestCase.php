<?php

namespace Portunus\Test\Model;

use Portunus\Test\UnitTestCase;

abstract class ModelUnitTestCase extends UnitTestCase
{
    private $freshDb = false;

    public function setUp()
    {
        if ($this->freshDb) {
            return;
        }

        $this->getContainer()->setParameter('doctrine.db.filename', 'portunus_test.sqlite');
        $dataDir = $this->getContainer()->getParameter('doctrine.db.data_dir');
        $dbFile = $this->getContainer()->getParameter('doctrine.db.filename');
        $dbFile = sprintf('%s/%s', $dataDir, $dbFile);

        if (file_exists($dbFile)) {
            unlink($dbFile);
        }

        $this->getContainer()->get('portunus.application')->createDb($this->getContainer());

        $this->freshDb = true;
    }

    public function persist($entityObject, $findCriteria)
    {
        $this->getEntityManager()->persist($entityObject);
        $this->getEntityManager()->flush();
        $repository = $this->getEntityManager()->getRepository(get_class($entityObject));
        $foundEntity = $repository->findOneBy($findCriteria);
        $this->assertInstanceOf(get_class($entityObject), $foundEntity, 'Entity object not found');
        $this->assertSame($entityObject, $foundEntity, 'Entity object not the same as found entity');
    }

    public function remove($entityObject, $findCriteria)
    {
        $this->getEntityManager()->persist($entityObject);
        $this->getEntityManager()->flush();

        $this->getEntityManager()->remove($entityObject);
        $this->getEntityManager()->flush();

        $repository = $this->getEntityManager()->getRepository(get_class($entityObject));
        $object = $repository->findOneBy($findCriteria);

        $this->assertNull($object, 'Entity object not removed');
    }
}
