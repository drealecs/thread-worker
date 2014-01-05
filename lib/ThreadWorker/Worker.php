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
            $task = $this->queue->start();
            try {
                $task->getTask()->run();
            } catch (\Exception $ex) {
            }
            $this->queue->end($task);
        }
    }
}
