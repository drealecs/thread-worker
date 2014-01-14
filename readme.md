drealecs/thread-worker
=============

PHP multi-thread-worker message/event library
[![Build Status](https://secure.travis-ci.org/drealecs/thread-worker.png?branch=master)](http://travis-ci.org/drealecs/thread-worker)

Introduction
------------

Thread-worker is a library that allows execution of tasks in parallel by multiple PHP processes
on the same computer or on different computers.

The library has a lot of concepts borrowed from other languages.



Concepts
--------

 - **Task** represents a function that should be executed asynchronously.

 - **Queue** is a queue of tasks. Tasks can be put into queue by a process and taken out for execution by another process.

 - **Executor** is an wrapper over a queue. It can be passed to running task so that those tasks can spawn more tasks.

 - **TaskResult** or **TaskException** are the result of a **Task**.

**Task**, **TaskResult** and **TaskException** are the entities that are being serialized as "messages" and write to/read from the queue.

API
---

### Task

A code that will be executed remotely cannot share variables or context with the calling code. When defining a function
that can be executed asynchronously it must be created as a Task by extending \ThreadWorker\Task and implementing method `run()`:
``` php
class AddTask extends ThreadWorker\Task
{
    public function run($a, $b)
    {
        returns $a + $b;
    }
}
```
and after that, the task can be used in this way:
``` php
$task = new AddTask(3, 5);
$result = $task();
```
and this will make `$result` equals 8.

Just like a function, a task can return a value or not.
