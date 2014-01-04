<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$type = $argv[1];
$worker = new ThreadWorker\Worker($type);
$worker->work();
