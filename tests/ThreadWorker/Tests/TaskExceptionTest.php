<?php
namespace ThreadWorker\Tests;

use ThreadWorker\TaskException;

class TaskExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testTaskExceptionEncapsulation()
    {
        $testMessage = 'testString';
        $testCode = 17;

        $innerException = new \Exception($testMessage, $testCode);

        $exception = new TaskException($innerException);

        $this->assertEquals($testMessage, $exception->getMessage());
        $this->assertEquals($testCode, $exception->getCode());
        $this->assertEquals(13, $exception->getLine());
        $this->assertEquals('TaskExceptionTest.php', basename($exception->getFile()));

        $serializedException = serialize($exception);
        $exception = unserialize($serializedException);

        $this->assertEquals($testMessage, $exception->getMessage());
        $this->assertEquals($testCode, $exception->getCode());
        $this->assertEquals(13, $exception->getLine());
        $this->assertEquals('TaskExceptionTest.php', basename($exception->getFile()));
    }
} 
