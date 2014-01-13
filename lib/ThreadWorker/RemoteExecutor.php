<?php
namespace ThreadWorker;

class RemoteExecutor extends QueueExecutor
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @param Queue $queue
     */
    public function __construct($queue)
    {
        parent::__construct($queue);
        $this->queue = $queue;
    }

    public function work()
    {
        while (true) {
            $remoteTask = $this->startTask();
            $this->runTask($remoteTask);
            $this->endTask($remoteTask);
        }
    }

    /**
     * @return RemoteTask
     */
    protected function startTask()
    {
        return $this->queue->start();
    }

    /**
     * @param RemoteTask $remoteTask
     */
    protected function runTask($remoteTask)
    {
        try {
            $task = $remoteTask->getTask();
            $result = $task($this);
            $taskResult = new TaskResult($result);
            $remoteTask->done($taskResult);
        } catch (\Exception $exception) {
            $taskException = new TaskException($exception);
            $remoteTask->fail($taskException);
        }
    }

    /**
     * @param RemoteTask $remoteTask
     */
    protected function endTask($remoteTask)
    {
        $this->queue->end($remoteTask);
    }

}
