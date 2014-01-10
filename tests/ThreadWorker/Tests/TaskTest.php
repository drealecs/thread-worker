<?php
namespace ThreadWorker\Tests;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    public function testTaskRun()
    {
        $task = $this->getMock('ThreadWorker\Task', array('run'), array('testParam1', 'testParam2'), 'MockTask');
        $task->expects($this->once())
            ->method('run')
            ->with('testParam1', 'testParam2')
            ->will($this->returnValue('testResult'));
        $this->assertEquals('testResult', $task());
    }
} 
