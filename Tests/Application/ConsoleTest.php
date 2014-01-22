<?php
namespace Kitpages\SemaphoreBundle\Tests\Application ;

use Kitpages\SemaphoreBundle\Tests\TestUtil\CommandTestCase;


class ConsoleTest extends CommandTestCase
{
    // note : this test is only used to see if configuration is well parsed
    public function testRunCommandSimple()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "");
        $this->assertContains('cache:clear', $output);
    }

    public function testAquire()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "kitpages:semaphore:aquire my_semaphore --data=12");
        $this->assertEquals('12', $output);
        $this->runCommand($client, "kitpages:semaphore:release my_semaphore");
    }


}