<?php
namespace ThreadWorker\Tests;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    public function testTaskRun()
    {
        $testParam1 = 'testString1';
        $testParam2 = 'testString2';
        $testReturnValue = 'testString3';

        $task = $this->getMock('ThreadWorker\Task', array('run'), array($testParam1, $testParam2));
        $task->expects($this->once())
            ->method('run')
            ->with($testParam1, $testParam2)
            ->will($this->returnValue($testReturnValue));

        $this->assertEquals($testReturnValue, $task());

        $serializedTask = serialize($task);
        $task = unserialize($serializedTask);
        
        $task->expects($this->once())
            ->method('run')
            ->with($testParam1, $testParam2)
            ->will($this->returnValue($testReturnValue));
        
        $this->assertEquals($testReturnValue, $task());
    }
    
    public function testTaskBadImplementation()
    {
        $this->setExpectedException('Exception');
        $task = $this->getMock('ThreadWorker\Task');
    }
    
} 
