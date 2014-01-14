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

Of course, the example above execute a task locally, synchronously. To execute it asynchronously we will need a Queue and an Executor.

### Queue

To synchronize working tasks a queue concept is being used.

There is a queue, someone puts a task in the queue and there are workers that take it and run it.


Interface of a task queue:

 - public function queue($task, $captureResult); - called to queue a task for execution. There are Tasks that don't return a result and Tasks that returns a result.
A task that does not returns a result is usually preferable because the calling code can do other things and get out of scope or even finish execution.
To accomplish this `$captureResult` must be `false` in which case the methods does not returns anything.
If we need a execute multiple task remotely and join their result we might need to pass second parameter as `true` and `queue()` method will return a task
identifier that can be used later to query and retrieve the task result.

 - public function start() - called by the script that can execute a task. This is a blocking method and it blocks until there is a task in the queue.
It returns a RemoteTask which is a container for the Task and it's TaskResult.

 - public function end($remoteTask) - called by the script that executed the task. It marks the task a being finished and it task is one that returns
a response, it stores the TaskResult.

 - public function getResult($taskId) - usually called by the script that queued the task for execution. It can be called only one time and is blocking
until the task finished to execute.

 - public function isQueued|isRunning|isFinished($taskId) - methods that can query the state of a task.

 - public function getQueueSize() and getRunningSize() - methods that can query the queue for it's current workflow capacity.

Currently there is only one implementation of Queue: \ThreadWorker\RedisQueue and there are plans for: AMQPQueue, MySQLQueue


### Executor

Executor wraps a queue and provide a simpler interface to queue task and work on tasks and get task results.

There is a QueueExecutor that has 2 methods:

 - void execute(Task $task) - adds the task to the queue
 - QueuedTask submit(Task $task) - adds the task to the queue and returns a QueuedTask instance that can be used to query task status and retrieve the task result.

Let's look at an example:

``` php
$queue = new ThreadWorker\RedisQueue('example');
$executor = new ThreadWorker\QueueExecutor($queue);

$task = new AddTask(3, 5);
$queuedTask = $executor->submit($task);
$result = $queuedTask->getResult()->getValue();
```

QueueExecutor is extended into RemoteExecutor that does the asynchronous running of the tasks.
Worker's code would look like this:

``` php
$queue = new ThreadWorker\RedisQueue('example');
$worker = new ThreadWorker\RemoteExecutor($queue);

$worker->work();
```

An instance of RemoteExecutor is passed as an extra parameter to the `run()` method of the task and can be use to queue more tasks.
