<?php
namespace Kitpages\SemaphoreBundle\Tests\Manager;

use Kitpages\SemaphoreBundle\Manager\Manager;
use Kitpages\SemaphoreBundle\Tests\BundleOrmTestCase;
use Doctrine\ORM\EntityManager;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

class ManagerTest extends BundleOrmTestCase
{
    /** @var  Manager */
    protected $manager;

    /** @var  Logger */
    protected $logger;

    /** @var  TestHandler */
    protected $loggerTestHandler;

    protected function setUp()
    {
        parent::setUp();

        $this->logger = new Logger('name');
        $this->loggerTestHandler = new TestHandler();
        $this->logger->pushHandler($this->loggerTestHandler);
        $this->manager = new Manager(
            $this->getEntityManager()->getConnection(),
            100000,
            4000000,
            $this->logger
        );
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testBasicSemaphore()
    {
        $startTime = microtime(true);
        $this->manager->aquire("my_key");
        $duration = microtime(true) - $startTime;
        $this->assertTrue($duration < 2);

        $this->manager->release("my_key");

        $this->manager->aquire("my_key");
        $duration2 = microtime(true) - $startTime;
        $this->assertTrue($duration2 < 3);

    }

    public function testExpiration()
    {
        $startTime = microtime(true);
        $this->manager->aquire("my_key");
        $duration = microtime(true) - $startTime;
        $this->assertTrue($duration < 2);

        $loggerRecordList = $this->loggerTestHandler->getRecords();
        $this->assertEquals(0, count($loggerRecordList));

        $this->manager->aquire("my_key");
        $duration2 = microtime(true) - $startTime;
        $this->assertTrue($duration2 > 4);

        $loggerRecordList = $this->loggerTestHandler->getRecords();
        $this->assertEquals(1, count($loggerRecordList));
        $record = $loggerRecordList[0];
        $message = $record["message"];
        $this->assertEquals(1, preg_match('/Dead lock detected at.+\\/Tests\\/Manager\\/ManagerTest\\.php\\(\d+\)/',$message));
    }
}
