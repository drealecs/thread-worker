<?php
namespace ThreadWorker;

class TaskLazyResult extends TaskResult
{
    /**
     * @var QueuedTask
     */
    private $queuedTask;

    /**
     * @param QueuedTask $queuedTask
     */
    public function __construct($queuedTask)
    {
        $this->queuedTask = $queuedTask;
    }

    public function getValue()
    {
        return $this->queuedTask->getResult()->getValue();
    }
}
