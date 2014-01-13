<?php
/**
 * Created by Philippe Le Van.
 * Date: 04/12/13
 */
namespace Kitpages\SemaphoreBundle\Manager;

use Doctrine\DBAL\Driver\Connection;
use Psr\Log\LoggerInterface;

class Manager
    implements ManagerInterface
{
    /** @var Connection */
    protected $connexion = null;

    /** @var int */
    protected $sleepTimeMicroseconds;

    /** @var  int */
    protected $deadlockMicroseconds;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct(
        Connection $connexion,
        $sleepTimeMicroseconds,
        $deadLockMicroseconds,
        LoggerInterface $logger
    )
    {
        $this->connexion = $connexion;
        $this->sleepTimeMicroseconds = $sleepTimeMicroseconds;
        $this->deadlockMicroseconds = $deadLockMicroseconds;
        $this->logger = $logger;
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
            $this->connexion->beginTransaction();
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
                $statement->execute();
                $this->connexion->commit();
                return;
            }

            $locked = $selectResult["locked"];
            $microtime = $selectResult["microtime"];

            if (true == $locked && $this->microSecondsTime() >= $microtime + $this->deadlockMicroseconds) {
                $backtraceList = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                $backtrace = $backtraceList[0];
                $now = new \DateTime();
                $this->logger->warning("Dead lock detected at ".$now->format(DATE_RFC2822)." in ".$backtrace["file"].'('.$backtrace["line"].')');
                $statement = $this->connexion->prepare(
                    "UPDATE kitpages_semaphore set `microtime`=:microtime WHERE `key`=:key"
                );
                $statement->bindValue(":key", $key, \PDO::PARAM_STR);
                $statement->bindValue(":microtime", $this->microSecondsTime(), \PDO::PARAM_INT);

                $statement->execute();
                $this->connexion->commit();
                return;

            } elseif ($locked == false) {
                $statement = $this->connexion->prepare(
                    "UPDATE kitpages_semaphore set `locked`=:locked, `microtime`=:microtime WHERE `key`=:key"
                );
                $statement->bindValue(":key", $key, \PDO::PARAM_STR);
                $statement->bindValue(":locked", true, \PDO::PARAM_BOOL);
                $statement->bindValue(":microtime", $this->microSecondsTime(), \PDO::PARAM_INT);

                $statement->execute();
                $this->connexion->commit();
                return;
            }
            $this->connexion->commit();
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