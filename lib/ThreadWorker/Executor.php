<?php
namespace ThreadWorker;

interface Executor {

    /**
     * @param Task $task
     */
    public function execute(Task $task);

    /**
     * @param Task $task
     * @return QueuedTask
     */
    public function submit(Task $task);
}
