<?php
namespace ThreadWorker;

class RemoteTask {

    /**
     * @var Task
     */
    private $task;

    /**
     * @var string|int
     */
    private $id;

    public function __construct($task, $id)
    {
        $this->task = $task;
        $this->id = $id;
    }

    public function getTask()
    {
        return $this->task;
    }

    public function getId()
    {
        return $this->task;
    }
}