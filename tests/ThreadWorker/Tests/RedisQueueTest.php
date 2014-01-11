<?php
namespace ThreadWorker\Tests;

use ThreadWorker\RedisQueue;

class RedisQueueTest extends QueueTest
{
    protected function getQueue()
    {
        return new RedisQueue('testList');
    }
}
