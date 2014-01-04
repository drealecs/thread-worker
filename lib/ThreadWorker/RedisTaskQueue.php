<?php
namespace ThreadWorker;

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
        $serializedTask = $this->redis->brPop($this->getRedisTaskListKey(), 0)[1];
        $task = unserialize($serializedTask);
        return new RemoteTask($task, null);
    }

    /**
     * @param RemoteTask $task
     */
    public function end(RemoteTask $task)
    {

    }

    private function getRedisTaskListKey()
    {
        return 'thread-worker-queue-' . $this->getType();
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return (int)$this->redis->lLen($this->getRedisTaskListKey());
    }
}
