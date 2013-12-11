<?php
/**
 * Created by Philippe Le Van.
 * Date: 04/12/13
 */

namespace Kitpages\SemaphoreBundle\Manager;

interface ManagerInterface
{
    /**
     * @param string $key
     */
    public function aquire($key);

    /**
     * @param string $key
     */
    public function release($key);
} 