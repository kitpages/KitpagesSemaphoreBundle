<?php
/**
 * Created by Philippe Le Van.
 * Date: 04/12/13
 */
namespace Kitpages\SemaphoreBundle\Manager;

use Doctrine\DBAL\Driver\Connection;
use Kitpages\SemaphoreBundle\Entity\Semaphore;

class Manager
    implements ManagerInterface
{
    /** @var Connection */
    protected $connexion = null;

    /** @var int */
    protected $sleepTimeMicroseconds;

    /** @var  int */
    protected $deadlockMicroseconds;

    public function __construct(Connection $connexion, $sleepTimeMicroseconds, $deadLockMicroseconds)
    {
        $this->connexion = $connexion;
        $this->sleepTimeMicroseconds = $sleepTimeMicroseconds;
        $this->deadlockMicroseconds = $deadLockMicroseconds;
    }

    protected function microSecondsTime()
    {

        $microtime = microtime(true);
        $microtime *= 1000000.0;
        $microtime = intval($microtime);

        return $microtime;
    }

    /**
     * @inheritdoc
     */
    public function aquire($key)
    {
        $locked = true;
        while ($locked == true) {
            $stmt = $this->connexion->prepare(
                " SELECT * from kitpages_semaphore where `key`= :key "
            );
            $stmt->bindValue(":key", $key, \PDO::PARAM_STR);
            $stmt->execute();
            $selectResult = $stmt->fetch(\PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$selectResult) {
                $statement = $this->connexion->prepare(
                    "INSERT INTO kitpages_semaphore (`key`, `locked`, `microtime`) VALUES (:key, :locked, :microtime)"
                );
                $statement->bindValue(":key", $key, \PDO::PARAM_STR);
                $statement->bindValue(":locked", true, \PDO::PARAM_BOOL);
                $statement->bindValue(":microtime", $this->microSecondsTime(), \PDO::PARAM_INT);

                return $statement->execute();
            }

            $locked = $selectResult["locked"];
            $microtime = $selectResult["microtime"];

            if (true == $locked && $this->microSecondsTime() >= $microtime + $this->deadlockMicroseconds) {
                $statement = $this->connexion->prepare(
                    "UPDATE kitpages_semaphore set `microtime`=:microtime WHERE `key`=:key"
                );
                $statement->bindValue(":key", $key, \PDO::PARAM_STR);
                $statement->bindValue(":microtime", $this->microSecondsTime(), \PDO::PARAM_INT);

                return $statement->execute();

            } elseif ($locked == false) {
                $statement = $this->connexion->prepare(
                    "UPDATE kitpages_semaphore set `locked`=:locked, `microtime`=:microtime WHERE `key`=:key"
                );
                $statement->bindValue(":key", $key, \PDO::PARAM_STR);
                $statement->bindValue(":locked", true, \PDO::PARAM_BOOL);
                $statement->bindValue(":microtime", $this->microSecondsTime(), \PDO::PARAM_INT);

                return $statement->execute();
            }

            usleep($this->sleepTimeMicroseconds);
        }
    }

    /**
     * @inheritdoc
     */
    public function release($key)
    {
        $statement = $this->connexion->prepare("UPDATE kitpages_semaphore set `locked`=:locked WHERE `key`=:key");
        $statement->bindValue(":key", $key, \PDO::PARAM_STR);
        $statement->bindValue(":locked", false, \PDO::PARAM_BOOL);

        return $statement->execute();
    }
}