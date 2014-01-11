<?php
namespace ThreadWorker;

interface Queue
{
    /**
     * @param Task $task
     * @param bool $captureResult
     * @return string|int
     */
    public function queue(Task $task, $captureResult);

    /**
     * @return RemoteTask
     */
    public function start();

    /**
     * @param RemoteTask $task
     */
    public function end(RemoteTask $task);

    /**
     * @param string|int $taskId
     * @return TaskResult|null
     * @throws TaskException
     */
    public function getResult($taskId);

    /**
     * @param string|int $taskId
     * @return bool
     */
    public function isQueued($taskId);

    /**
     * @param string|int $taskId
     * @return bool
     */
    public function isRunning($taskId);

    /**
     * @param string|int $taskId
     * @return bool
     */
    public function isFinished($taskId);

    /**
     * @return int
     */
    public function getQueueSize();

    /**
     * @return int
     */
    public function getRunningSize();
}
