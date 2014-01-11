<?php
namespace ThreadWorker;

class TaskLazyResult extends TaskResult
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var string|int
     */
    private $id;

    /**
     * @param Queue $queue
     * @param string|int $id
     */
    public function __construct($queue, $id)
    {
        $this->queue = $queue;
        $this->id = $id;
    }

    public function getValue()
    {
        return $this->queue->getResult($this->id)->getValue();
    }
}
