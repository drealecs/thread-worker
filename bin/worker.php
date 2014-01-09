<?php
set_time_limit(0);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$type = $argv[1];
$worker = new ThreadWorker\Worker(new ThreadWorker\RedisQueue($type));
$worker->work();
