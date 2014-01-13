<?php
namespace ThreadWorker;

class RedisQueueExecutor extends  QueueExecutor
{
    /**
     * @var RedisQueue[]
     */
    private static $queueInstances = array();

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $queue = self::getRedisQueueInstance($type);
        parent::__construct($queue);
    }

    /**
     * @param string $type
     * @return RedisQueue
     */
    private static function getRedisQueueInstance($type)
    {
        if (!isset(self::$queueInstances[$type])) {
            self::$queueInstances[$type] = new RedisQueue($type);
        }
        return self::$queueInstances[$type];
    }

}
