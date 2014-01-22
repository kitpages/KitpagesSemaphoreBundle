<?php
namespace Kitpages\SemaphoreBundle\Tests\Application ;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Kitpages\SemaphoreBundle\Tests\TestUtil\CommandTestCase;

/**
 * @group ordering
 */
class OrderingTest extends CommandTestCase
{
    private function getCmd($commandName, $data=null)
    {
        $client = self::createClient();

        // find php executable
        $phpFinder = new PhpExecutableFinder();
        $phpCmd = $phpFinder->find();

        $cmd =
            escapeshellcmd($phpCmd).' '.
            escapeshellarg($client->getContainer()->getParameter("kernel.root_dir").'/console').' '.
            escapeshellarg($commandName).' '.
            escapeshellarg('my_semaphore')
        ;
        if (!is_null($data)) {
            $cmd .= ' '.escapeshellarg('--data='.$data);
        }
//        echo $cmd."\n";
        return $cmd;
    }
    public function testRunCommandSimple()
    {
        $process = new Process($this->getCmd('kitpages:semaphore:aquire', 12));
        $process->start();
        while ($process->isRunning()) {
            sleep(1);
        }
        $output = $process->getOutput();
        $this->assertEquals('12', $output);
        $process = new Process($this->getCmd('kitpages:semaphore:release'));
        $process->run();
    }

    public function testOrdering()
    {
        $logFile = "/tmp/kitpages_semaphore_phpunit.log";
        $nbProcess = 9;
        @unlink($logFile);
        $processList = array();
        $now = time();
        for ($i = 1 ; $i <= $nbProcess ; $i++) {
            $processList["process_$i"] = new Process($this->getCmd('kitpages:semaphore:aquire', $i).' >> '.$logFile);
            $processList["process_$i"]->start();
//            echo (time() - $now) . "- aquire started $i\n";
        }
        sleep(1);
        for ($i = 1 ; $i <= $nbProcess ; $i++) {
            $process = new Process($this->getCmd('kitpages:semaphore:release'));
            $process->run();
//            echo (time() - $now) . " - release ran $i\n";
        }
        sleep(1);
        $content = file_get_contents($logFile);
        $this->assertEquals($nbProcess, strlen($content));
    }
}