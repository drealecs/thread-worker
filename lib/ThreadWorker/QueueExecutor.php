<?php
namespace ThreadWorker;

class QueueExecutor implements Executor
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
        $this->queue = $queue;
    }

    public function execute($task)
    {
        $this->queue->queue($task, false);
    }

    public function submit($task)
    {
        $taskId = $this->queue->queue($task, true);
        return new QueuedTask($task, $taskId, $this->queue);
    }


} 
