<?php
namespace ThreadWorker;

abstract class RedisTask extends Task
{
    /**
     * @param string $type
     */
    public function execute($type)
    {
        $executor = new RedisQueueExecutor($type);
        $executor->execute($this);
    }

    /**
     * @param string $type
     * @return \ThreadWorker\TaskResult
     */
    public function submit($type)
    {
        $executor = new RedisQueueExecutor($type);
        $queuedTask = $executor->submit($this);
        return $queuedTask->getLazyResult();
    }
}
