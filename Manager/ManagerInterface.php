<?php
/**
 * Created by Philippe Le Van.
 * Date: 04/12/13
 */

namespace Kitpages\SemaphoreBundle\Manager;

use Kitpages\SemaphoreBundle\Entity\Semaphore;

interface ManagerInterface
{
    public function aquire($key);

    public function release($key);
} 