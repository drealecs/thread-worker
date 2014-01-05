<?php
namespace ThreadWorker;

class Worker {

    /**
     * @var TaskQueue
     */
    private $queue;

    /**
     * @param TaskQueue $queue
     */
    public function __construct($queue)
    {
        $this->queue = $queue;
    }

    public function work()
    {
        while (true) {
            $task = $this->startTask();
            try {
                $this->runTask($task);
            } catch (\Exception $ex) {
            }
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
     * @param RemoteTask $task
     */
    protected function runTask($task)
    {
        $task->getTask()->run();
    }

    /**
     * @param RemoteTask $task
     */
    protected function endTask($task)
    {
        $this->queue->end($task);
    }

}
