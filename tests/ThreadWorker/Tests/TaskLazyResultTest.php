<?php
namespace ThreadWorker\Tests;

use Rhumsaa\Uuid\Uuid;
use ThreadWorker\TaskException;
use ThreadWorker\TaskLazyResult;
use ThreadWorker\TaskResult;

class TaskLazyResultTest extends \PHPUnit_Framework_TestCase
{
    public function testTaskLazyResult()
    {
        $testValue = 'testString';
        $testId = Uuid::uuid4();

        $queue = $this->getMock('ThreadWorker\Queue');
        $queue->expects($this->any())
            ->method('getResult')
            ->with($testId)
            ->will($this->returnValue(new TaskResult($testValue)));

        $result = new TaskLazyResult($queue, $testId);

        $this->assertEquals($testValue, $result->getValue());
    }

    public function testTaskLazyResultException()
    {
        $testId = Uuid::uuid4();

        $queue = $this->getMock('ThreadWorker\Queue');
        $queue->expects($this->any())
            ->method('getResult')
            ->with($testId)
            ->will($this->throwException(new TaskException(new \Exception())));

        $result = new TaskLazyResult($queue, $testId);

        $this->setExpectedException('ThreadWorker\TaskException');
        $result->getValue();
    }

} 
