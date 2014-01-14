<?php
namespace Kitpages\SemaphoreBundle\Tests\Configuration;

use Kitpages\SemaphoreBundle\Tests\TestUtil\CommandTestCase;

class ConsoleTest extends CommandTestCase
{
    // note : this test is only used to see if configuration is well parsed
    public function testRunCommandSimple()
    {
        $client = self::createClient();

        $output = $this->runCommand($client, "");
        $this->assertContains('doctrine:mapping:convert', $output);
    }

}