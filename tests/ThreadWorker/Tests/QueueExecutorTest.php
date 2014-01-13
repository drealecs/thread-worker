<?php
namespace ThreadWorker\Tests;

use Rhumsaa\Uuid\Uuid;
use ThreadWorker\QueueExecutor;
use ThreadWorker\TaskResult;

class QueueExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutorQueue()
    {

        $task1 = $this->getMock('ThreadWorker\Task', array('run'));
        $task2 = $this->getMock('ThreadWorker\Task', array('run'));
        $taskId = (string)Uuid::uuid4();
        $testResult = 'testString';

        $queue = $this->getMock('ThreadWorker\Queue');
        $queue->expects($this->once())
            ->method('queue')
            ->with($task1, false)
            ->will($this->returnValue(null));
        $executor = new QueueExecutor($queue);
        $this->assertNull($executor->execute($task1));

        $queue = $this->getMock('ThreadWorker\Queue');
        $queue->expects($this->once())
            ->method('queue')
            ->with($task2, true)
            ->will($this->returnValue($taskId));
        $queue->expects($this->once())
            ->method('getResult')
            ->with($taskId)
            ->will($this->returnValue(new TaskResult($testResult)));

        $executor = new QueueExecutor($queue);
        $queuedTask = $executor->submit($task2);
        $this->assertInstanceOf('ThreadWorker\QueuedTask', $queuedTask);
        $taskResult = $queuedTask->getResult();
        $this->assertEquals($testResult, $taskResult->getValue());
    }

} 
