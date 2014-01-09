<?php
namespace ThreadWorker;

final class RemoteTask {

    /**
     * @var Task
     */
    private $task;

    /**
     * @var string|int
     */
    private $id;

    /**
     * @var TaskResult
     */
    private $result;

    /**
     * @var TaskException
     */
    private $exception;

    public function __construct(Task $task, $id)
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
        return $this->id;
    }

    public function done($result)
    {
        if (!$this->isFinished()) {
            $this->result = $result;
        }
    }

    public function fail($exception)
    {
        if (!$this->isFinished()) {
            $this->exception = $exception;
        }
    }

    private function isFinished()
    {
        return isset($this->result) || isset($this->exception);
    }

    public function getResult()
    {
        return isset($this->result) ? $this->result : $this->exception;
    }
}
