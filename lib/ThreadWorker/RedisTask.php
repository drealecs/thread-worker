<?php
namespace ThreadWorker;

abstract class RedisTask extends Task
{

    /**
     * @var RedisQueueExecutor[]
     */
    private static $executorInstances = array();

    /**
     * @param string $type
     * @return RedisQueueExecutor
     */
    private static function getRedisQueueExecutorInstance($type)
    {
        if (!isset(self::$executorInstances[$type])) {
            self::$executorInstances[$type] = new RedisQueueExecutor($type);
        }
        return self::$executorInstances[$type];
    }

    /**
     * @param string $type
     */
    public function execute($type)
    {
        $this->executeTask($this, $type);
    }

    /**
     * @param string $type
     * @return \ThreadWorker\TaskResult
     */
    public function submit($type)
    {
        return $this->submitTask($this, $type);
    }

    /**
     * @param Task $task
     * @param string $type
     */
    public function executeTask($task, $type)
    {
        $executor = self::getRedisQueueExecutorInstance($type);
        $executor->execute($task);
    }

    /**
     * @param Task $task
     * @param string $type
     * @return \ThreadWorker\TaskResult
     */
    public function submitTask($task, $type)
    {
        $executor = self::getRedisQueueExecutorInstance($type);
        $queuedTask = $executor->submit($task);
        return $queuedTask->getLazyResult();
    }



}
