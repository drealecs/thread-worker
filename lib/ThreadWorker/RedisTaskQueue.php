<?php
namespace ThreadWorker;

use Rhumsaa\Uuid\Uuid;

class RedisTaskQueue extends TaskQueue
{
    /**
     * @var \Redis
     */
    private $redis;

    public function __construct($type)
    {
        parent::__construct($type);
        $this->redis = new \Redis();
        $this->redis->connect('localhost');
    }

    public function queue(Task $task)
    {
        $serializedTask = serialize($task);
        $this->redis->lPush($this->getRedisTaskListKey(), $serializedTask);
    }

    /**
     * @return RemoteTask
     */
    public function start()
    {
        do {
            if (rand(1, 100) == 17) {
                $this->requeueOldWorkingTasks();
            }
            do {
                $blockingResult = $this->redis->brPop($this->getRedisTaskListKey(), 4);
                if (empty($blockingResult)) {
                    $this->requeueOldWorkingTasks();
                }
            } while (empty($blockingResult));

            $serializedTask = $blockingResult[1];
            $task = unserialize($serializedTask);

            if ($task instanceof Task) {
                $success = true;
            } else {
                $success = false;
                $this->redis->lPush($this->getRedisTaskErrorListKey(), $serializedTask);
            }
        } while (!$success);

        $taskId = Uuid::uuid4();

        $serializedTaskPrepended = time() . '|' .  $serializedTask;
        $this->redis->hSet($this->getRedisTaskWorkingListKey(), $taskId, $serializedTaskPrepended);

        return new RemoteTask($task, $taskId);
    }

    private function requeueOldWorkingTasks()
    {
        $tasks = $this->redis->hGetAll($this->getRedisTaskWorkingListKey());
        foreach ($tasks as $taskId => $serializedTaskPrepended) {
            list($taskTime, $serializedTask) = explode('|', $serializedTaskPrepended, 2);
            if (time() > 60 + (int)$taskTime) {
                $this->redis->hDel($this->getRedisTaskWorkingListKey(), $taskId);
                $this->redis->rPush($this->getRedisTaskListKey(), $serializedTask);
            }
        }
    }

    /**
     * @param RemoteTask $task
     */
    public function end(RemoteTask $task)
    {
        $this->redis->hDel($this->getRedisTaskWorkingListKey(), $task->getId());
    }

    private function getRedisTaskListKey()
    {
        return 'thread-worker-queue-' . $this->getType();
    }

    private function getRedisTaskWorkingListKey()
    {
        return 'thread-worker-progress-' . $this->getType();
    }

    private function getRedisTaskErrorListKey()
    {
        return 'thread-worker-error-' . $this->getType();
    }

    public function getQueueSize()
    {
        return (int)$this->redis->lLen($this->getRedisTaskListKey());
    }

    public function getWorkingSize()
    {
        return (int)$this->redis->hLen($this->getRedisTaskWorkingListKey());
    }
}
