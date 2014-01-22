<?php
namespace Kitpages\SemaphoreBundle\Tests\Manager;

use Kitpages\SemaphoreBundle\Manager\Manager;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

class ManagerTest extends \PHPUnit_Framework_TestCase
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
        $this->loggerTestHandler = new TestHandler(Logger::WARNING);
        $this->logger->pushHandler($this->loggerTestHandler);
        foreach (glob(__DIR__.'/../app/data/kitpages_semaphore/*.csv') as $file) {
            unlink($file);
        }
        $this->manager = new Manager(
            100000,
            4000000,
            $this->logger,
            __DIR__.'/../app/data/kitpages_semaphore'
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
