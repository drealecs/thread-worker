<?php
namespace ThreadWorker;

class Worker {

    /**
     * @var TaskQueue
     */
    private $queue;

    public function __construct($type)
    {
        $this->queue = new RedisTaskQueue($type);
    }

    public function work()
    {
        while (true) {
            $task = $this->queue->start();
            $task->getTask()->run();
            $this->queue->end($task);
        }
    }
}
