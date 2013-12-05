<?php
/**
 * Created by Philippe Le Van.
 * Date: 04/12/13
 */
namespace Kitpages\SemaphoreBundle\Entity;


class Semaphore
{
    /** @var  string */
    protected $key;
    /** @var  boolean */
    protected $locked;
    /** @var  int */
    protected $microtime;

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }


    /**
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * @param int $microtime
     */
    public function setMicrotime($microtime)
    {
        $this->microtime = $microtime;
    }

    /**
     * @return int
     */
    public function getMicrotime()
    {
        return $this->microtime;
    }
} 