<?php
namespace ThreadWorker;

interface Executor {

    /**
     * @param Task $task
     */
    public function execute($task);

    /**
     * @param Task $task
     * @return QueuedTask
     */
    public function submit($task);
}
