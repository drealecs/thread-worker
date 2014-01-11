<?php
namespace ThreadWorker\Tests;

use ThreadWorker\Queue;
use ThreadWorker\TaskException;
use ThreadWorker\TaskResult;

abstract class QueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Queue
     */
    private $queue;
    /**
     * @return Queue
     */
    abstract protected function getQueue();

    protected function setUp()
    {
        $this->queue = $this->getQueue();
        $this->queue->clear();
    }


    public function testQueue()
    {
        $task = $this->getMockTask();
        $taskId = $this->queue->queue($task, false);

        $this->assertNull($taskId);

        $task = $this->getMockTask();
        $taskId = $this->queue->queue($task, true);

        $this->assertNotNull($taskId);
        $this->assertTrue($this->queue->isQueued($taskId));
        $this->assertFalse($this->queue->isRunning($taskId));
        $this->assertFalse($this->queue->isFinished($taskId));
    }

    public function testStart()
    {
        $task = $this->getMockTask();
        $this->queue->queue($task, false);
        $remoteTask = $this->queue->start();

        $this->assertEquals($task, $remoteTask->getTask());

        $task = $this->getMockTask();
        $taskId = $this->queue->queue($task, true);
        $remoteTask = $this->queue->start();

        $this->assertEquals($task, $remoteTask->getTask());
        $this->assertFalse($this->queue->isQueued($taskId));
        $this->assertTrue($this->queue->isRunning($taskId));
        $this->assertFalse($this->queue->isFinished($taskId));
    }

    public function testEnd()
    {
        $task = $this->getMockTask();
        $this->queue->queue($task, false);
        $remoteTask = $this->queue->start();
        $remoteTask->done(new TaskResult(null));
        $this->queue->end($remoteTask);

        $task = $this->getMockTask();
        $taskId = $this->queue->queue($task, true);
        $remoteTask = $this->queue->start();
        $remoteTask->done(new TaskResult(null));
        $this->queue->end($remoteTask);

        $this->assertFalse($this->queue->isQueued($taskId));
        $this->assertFalse($this->queue->isRunning($taskId));
        $this->assertTrue($this->queue->isFinished($taskId));
    }

    public function testGetResult()
    {
        $result = 'testString';

        $task = $this->getMockTask();
        $taskId = $this->queue->queue($task, true);
        $remoteTask = $this->queue->start();
        $remoteTask->done(new TaskResult($result));
        $this->queue->end($remoteTask);
        $taskResult = $this->queue->getResult($taskId);

        $this->assertEquals($result, $taskResult->getValue());
        $this->assertFalse($this->queue->isQueued($taskId));
        $this->assertFalse($this->queue->isRunning($taskId));
        $this->assertFalse($this->queue->isFinished($taskId));
    }

    public function testGetResultException()
    {
        $task = $this->getMockTask();
        $taskId = $this->queue->queue($task, true);
        $remoteTask = $this->queue->start();
        $remoteTask->fail(new TaskException(new \Exception()));
        $this->queue->end($remoteTask);

        $this->setExpectedException('ThreadWorker\TaskException');
        $this->queue->getResult($taskId);
    }

    public function testQueueSizes()
    {
        $this->queue->queue($this->getMockTask(), true);
        $this->queue->queue($this->getMockTask(), false);
        $this->assertEquals(2, $this->queue->getQueueSize());
        $this->assertEquals(0, $this->queue->getRunningSize());
        $this->queue->queue($this->getMockTask(), true);
        $this->assertEquals(3, $this->queue->getQueueSize());
        $remoteTask1 = $this->queue->start();
        $this->assertEquals(2, $this->queue->getQueueSize());
        $this->assertEquals(1, $this->queue->getRunningSize());
        $remoteTask2 = $this->queue->start();
        $this->assertEquals(1, $this->queue->getQueueSize());
        $this->assertEquals(2, $this->queue->getRunningSize());
        $remoteTask1->done(new TaskResult(null));
        $this->queue->end($remoteTask1);
        $this->assertEquals(1, $this->queue->getQueueSize());
        $this->assertEquals(1, $this->queue->getRunningSize());
        $remoteTask3 = $this->queue->start();
        $remoteTask2->fail(new TaskException(new \Exception()));
        $this->queue->end($remoteTask2);
        $this->assertEquals(0, $this->queue->getQueueSize());
        $this->assertEquals(1, $this->queue->getRunningSize());
        $remoteTask3->done(new TaskResult(null));
        $this->queue->end($remoteTask3);
        $this->assertEquals(0, $this->queue->getQueueSize());
        $this->assertEquals(0, $this->queue->getRunningSize());
    }

    private function getMockTask($arguments = array())
    {
        return $this->getMock('ThreadWorker\Task', array('run'), $arguments);
    }
    
}
