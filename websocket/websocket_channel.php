<?php
require_once __DIR__ . '/vendor/autoload.php';
 
use Workerman\Worker;
use Channel\Server;

$channel_server = new Server('127.0.0.1', 8006);

Worker::runAll();