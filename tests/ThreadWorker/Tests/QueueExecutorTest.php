<?php
namespace ThreadWorker\Tests;

use Rhumsaa\Uuid\Uuid;
use ThreadWorker\QueueExecutor;

class QueueExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutorQueue()
    {

        $task1 = $this->getMock('ThreadWorker\Task', array('run'));
        $task2 = $this->getMock('ThreadWorker\Task', array('run'));
        $taskId = (string)Uuid::uuid4();

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
            ->with($taskId);

        $executor = new QueueExecutor($queue);
        $queuedTask = $executor->submit($task2);
        $this->assertEquals($task2, $queuedTask->getTask());

        $queuedTask->getResult();

    }

} 
