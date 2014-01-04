<?php
namespace ThreadWorker;

abstract class TaskQueue
{

    private $type;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return (string)$this->type;
    }

    abstract public function queue(Task $task);

    /**
     * @return RemoteTask
     */
    abstract public function start();

    /**
     * @param RemoteTask $task
     */
    abstract public function end(RemoteTask $task);

    /**
     * @return int
     */
    abstract public function getQueueSize();

    /**
     * @return int
     */
    abstract public function getWorkingSize();
}
