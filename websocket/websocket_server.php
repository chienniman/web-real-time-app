<?php
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Channel\Client;

$ws_worker = new Worker("websocket://127.0.0.1:8005");
 
$ws_worker->onWorkerStart = function ($worker) {
    Client::connect('127.0.0.1', 8006);

    Client::on('broadcast', function ($event_data) use ($worker) {
        foreach ($worker->connections as $connection) {
            $connection->send($event_data);
        }
    });
};
 
$ws_worker->onMessage = function ($connection, $data) use ($ws_worker) {
    Channel\Client::publish('broadcast', $data);
};
 
$ws_worker->onClose = function ($connection) {
    echo "連線已關閉\n";
};

Worker::runAll();