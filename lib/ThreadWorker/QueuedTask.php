<?php
namespace ThreadWorker;

final class QueuedTask {

    /**
     * @var Task
     */
    private $task;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var string|int
     */
    private $id;

    public function __construct(Task $task, $id, Queue $queue)
    {
        $this->task = $task;
        $this->queue = $queue;
        $this->id = $id;
    }

    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return bool
     */
    public function isQueued()
    {
        return $this->queue->isQueued($this->id);
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->queue->isRunning($this->id);
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->queue->isFinished($this->id);
    }

    /**
     * @return TaskResult
     * @throws TaskException
     */
    public function getResult()
    {
        return $this->queue->getResult($this->id);
    }

}
