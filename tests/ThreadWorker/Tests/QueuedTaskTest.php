<?php
namespace ThreadWorker\Tests;

use Rhumsaa\Uuid\Uuid;
use ThreadWorker\QueuedTask;
use ThreadWorker\TaskResult;

class QueuedTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testQueueBinding()
    {
        $taskId = (string)Uuid::uuid4();
        $testResult = 'testString';


        $queue = $this->getMock('ThreadWorker\Queue');
        $queue->expects($this->once())->method('isQueued')->with($taskId)->will($this->returnValue(true));
        $queue->expects($this->once())->method('isRunning')->with($taskId)->will($this->returnValue(true));
        $queue->expects($this->once())->method('isFinished')->with($taskId)->will($this->returnValue(true));
        $queue->expects($this->once())->method('getResult')->with($taskId)->will($this->returnValue(new TaskResult($testResult)));

        $queuedTask = new QueuedTask($taskId, $queue);

        $this->assertTrue($queuedTask->isQueued());
        $this->assertTrue($queuedTask->isRunning());
        $this->assertTrue($queuedTask->isFinished());
        $taskResult = $queuedTask->getResult();
        $this->assertInstanceOf('ThreadWorker\TaskResult', $taskResult);
        $this->assertEquals($testResult, $taskResult->getValue());
    }

    public function testLazyResultCreation()
    {
        $taskId = (string)Uuid::uuid4();
        $testResult = 'testString';


        $queue = $this->getMock('ThreadWorker\Queue');
        $queue->expects($this->once())->method('getResult')->with($taskId)->will($this->returnValue(new TaskResult($testResult)));

        $queuedTask = new QueuedTask($taskId, $queue);

        $taskResult = $queuedTask->getLazyResult();
        $this->assertInstanceOf('ThreadWorker\TaskLazyResult', $taskResult);
        $this->assertEquals($testResult, $taskResult->getValue());
    }
} 
