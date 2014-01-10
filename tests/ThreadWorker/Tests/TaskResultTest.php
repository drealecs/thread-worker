<?php
namespace ThreadWorker\Tests;

use ThreadWorker\TaskResult;

class TaskResultTest extends \PHPUnit_Framework_TestCase
{
    public function testTaskResultEncapsulation()
    {
        $testValue = 'testString';
        $result = new TaskResult($testValue);

        $this->assertEquals($testValue, $result->getValue());
        
        $serializedResult = serialize($result);
        $result = unserialize($serializedResult);

        $this->assertEquals($testValue, $result->getValue());
    }
} 
