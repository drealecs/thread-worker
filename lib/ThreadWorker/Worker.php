<?php
namespace ThreadWorker;

class Worker {

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @param Queue $queue
     */
    public function __construct($queue)
    {
        $this->queue = $queue;
    }

    public function work()
    {
        while (true) {
            $task = $this->startTask();
            $this->runTask($task);
            $this->endTask($task);
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
            $result = $task();
            $taskResult = new TaskResult($result);
            $remoteTask->done($taskResult);
        } catch (\Exception $exception) {
            $taskException = new TaskException($exception);
            $remoteTask->fail($taskException);
        }
    }

    /**
     * @param RemoteTask $task
     */
    protected function endTask($task)
    {
        $this->queue->end($task);
    }

}
