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

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param TaskResult $result
     */
    public function done($result)
    {
        if (!$this->isFinished()) {
            $this->result = $result;
        }
    }

    /**
     * @param TaskException $exception
     */
    public function fail($exception)
    {
        if (!$this->isFinished()) {
            $this->exception = $exception;
        }
    }

    /**
     * @return bool
     */
    private function isFinished()
    {
        return isset($this->result) || isset($this->exception);
    }

    /**
     * @return TaskException|TaskResult
     */
    public function getResult()
    {
        return isset($this->result) ? $this->result : $this->exception;
    }
}
