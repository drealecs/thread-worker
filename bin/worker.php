<?php
set_time_limit(0);

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

if ($argc < 2) {
    echo "Worker call must be with: worker queue_list\n";
    die;
}
$type = $argv[1];
$worker = new ThreadWorker\Worker(new ThreadWorker\RedisQueue($type));
$worker->work();
