<?php
namespace ThreadWorker;

use Rhumsaa\Uuid\Uuid;

class RedisQueue implements Queue
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var \Redis
     */
    private $redis;

    public function __construct($type)
    {
        $this->type = $type;
        $this->redis = new \Redis();
        $this->redis->connect('localhost');
    }

    public function queue(Task $task, $captureResult)
    {
        $taskId = (string)Uuid::uuid4();
        $serializedTask = serialize($task);

        $this->redis->multi();
        $this->redis->hSet($this->getTaskKey(), $taskId, $serializedTask);
        $this->redis->lPush($this->getTaskQueueKey(), $taskId);
        $this->redis->hSet($this->getTaskQueueTimeKey(), $taskId, time());
        if ($captureResult) {
            $this->redis->hSet($this->getTaskResultKey(), $taskId, 'TaskNotFinished');
        }
        $this->redis->exec();

        return $taskId;
    }

    public function start()
    {
        if (rand(0, 99) == 0) {
            $this->requeueOldWorkingTasks();
        }

        do {
            $taskId = $this->redis->brpoplpush($this->getTaskQueueKey(), $this->getTaskRunKey(), 1);
            if (empty($taskId) && rand(0, 9) == 0) {
                $this->requeueOldWorkingTasks();
            }
        } while (empty($taskId));
        $this->redis->hSet($this->getTaskStartTimeKey(), $taskId, time());

        $serializedTask = $this->redis->hGet($this->getTaskKey(), $taskId);
        $task = unserialize($serializedTask);

        return new RemoteTask($task, $taskId);
    }

    private function requeueOldWorkingTasks()
    {
        $taskIds = array_unique($this->redis->lRange($this->getTaskRunKey(), 0, -1));
        foreach ($taskIds as $taskId) {
            $time = $this->redis->hGet($this->getTaskStartTimeKey(), $taskId);
            if (!empty($time) && time() > 60 + (int)$time) {
                $this->redis->multi();
                $this->redis->rPush($this->getTaskQueueKey(), $taskId);
                $this->redis->lRem($this->getTaskRunKey(), $taskId, 1);
                $this->redis->hDel($this->getTaskStartTimeKey(), $taskId);
            }
        }
    }

    public function end(RemoteTask $task)
    {
        $taskId = $task->getId();

        $captureResult = 'TaskNotFinished' === $this->redis->hGet($this->getTaskResultKey(), $taskId);

        $this->redis->multi();
        $this->redis->lRem($this->getTaskRunKey(), $taskId, 0);
        $this->redis->hDel($this->getTaskStartTimeKey(), $taskId);
        $this->redis->lRem($this->getTaskQueueKey(), $taskId, 0);
        $this->redis->hDel($this->getTaskQueueTimeKey(), $taskId);
        $this->redis->hDel($this->getTaskKey(), $taskId);
        if ($captureResult) {
            $taskResult = $task->getResult();
            $serializedTaskResult = serialize($taskResult);
            $this->redis->hSet($this->getTaskResultKey(), $taskId, $serializedTaskResult);
            $this->redis->lPush($this->getTaskResultReadyKey($taskId), 'true');
        }
        $this->redis->exec();
    }

    public function getResult($taskId)
    {
        $serializedTaskResult = $this->redis->hGet($this->getTaskResultKey(), $taskId);
        if (empty($serializedTaskResult)) {
            return null;
        }

        do {
            $resultReady = $this->redis->blPop($this->getTaskResultReadyKey($taskId), 1);
        } while (empty($resultReady));

        $serializedTaskResult = $this->redis->hGet($this->getTaskResultKey(), $taskId);
        $taskResult = unserialize($serializedTaskResult);

        $this->redis->hDel($this->getTaskResultKey(), $taskId);

        return $taskResult;
    }

    private function getTaskQueueKey()
    {
        return 'thread-worker-queue-' . $this->type;
    }

    private function getTaskRunKey()
    {
        return 'thread-worker-run-' . $this->type;
    }

    private function getTaskKey()
    {
        return 'thread-worker-task-' . $this->type;
    }

    private function getTaskResultKey()
    {
        return 'thread-worker-result-' . $this->type;
    }

    private function getTaskResultReadyKey($taskId)
    {
        return 'thread-worker-result-ready-' . $this->type . '-' . $taskId;
    }

    private function getTaskQueueTimeKey()
    {
        return 'thread-worker-queue-time-' . $this->type;
    }

    private function getTaskStartTimeKey()
    {
        return 'thread-worker-start-time-' . $this->type;
    }

    public function getQueueSize()
    {
        return (int)$this->redis->lLen($this->getTaskQueueKey());
    }

    public function getRunningSize()
    {
        return (int)$this->redis->hLen($this->getTaskRunKey());
    }

    public function isQueued($taskId)
    {
        $this->redis->multi();
        $this->redis->hExists($this->getTaskKey(), $taskId);
        $this->redis->hExists($this->getTaskStartTimeKey(), $taskId);
        list($taskExists, $startTimeExists) = $this->redis->exec();
        return $taskExists && !$startTimeExists;
    }

    /**
     * @param string|int $taskId
     * @return bool
     */
    public function isRunning($taskId)
    {
        $this->redis->multi();
        $this->redis->hExists($this->getTaskKey(), $taskId);
        $this->redis->hExists($this->getTaskStartTimeKey(), $taskId);
        list($taskExists, $startTimeExists) = $this->redis->exec();
        return $taskExists && $startTimeExists;
    }

    /**
     * @param string|int $taskId
     * @return bool
     */
    public function isFinished($taskId)
    {
        return $this->redis->hExists($this->getTaskResultKey(), $taskId);
    }

}
