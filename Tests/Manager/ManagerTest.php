<?php
namespace Kitpages\SemaphoreBundle\Tests\Manager;

use Kitpages\SemaphoreBundle\Manager\Manager;
use Kitpages\SemaphoreBundle\Tests\BundleOrmTestCase;
use Doctrine\ORM\EntityManager;

class ManagerTest extends BundleOrmTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testBasicSemaphore()
    {
        $manager = new Manager($this->getEntityManager()->getConnection(), 500000, 4000000);
        $startTime = microtime(true);
        $manager->aquire("my_key");
        $duration = microtime(true) - $startTime;
        $this->assertTrue($duration < 2);

        $manager->release("my_key");

        $manager->aquire("my_key");
        $duration2 = microtime(true) - $startTime;
        $this->assertTrue($duration2 < 3);

    }

    public function testExpiration()
    {
        $manager = new Manager($this->getEntityManager()->getConnection(), 500000, 4000000);
        $startTime = microtime(true);
        $manager->aquire("my_key");
        $duration = microtime(true) - $startTime;
        $this->assertTrue($duration < 2);

        $manager->aquire("my_key");
        $duration2 = microtime(true) - $startTime;
        $this->assertTrue($duration2 > 4);

    }
}
