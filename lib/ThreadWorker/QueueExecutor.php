<?php
namespace ThreadWorker;

class QueueExecutor implements Executor
{
    /**
     * @var Queue
     */
    private $queue;

    public function __construct($queue)
    {
        $this->queue = $queue;
    }

    public function execute(Task $task)
    {
        $this->queue->queue($task, false);
    }

    public function submit(Task $task)
    {
        $taskId = $this->queue->queue($task, true);
        return new QueuedTask($task, $taskId, $this->queue);
    }


} 
