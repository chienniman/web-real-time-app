<?php
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Channel\Client;
use Workerman\Timer;

define('HEARTBEAT_TIME', 55);

$ws_worker = new Worker("websocket://127.0.0.1:8005");

$ws_worker->onWorkerStart = function ($worker) {
    Client::connect('127.0.0.1', 8006);

    Client::on(
        'broadcast',
        function ($event_data) use ($worker) {
            foreach ($worker->connections as $connection) {
                $connection->send($event_data);
            }
        }
    );

    Timer::add(
        10,
        function () use ($worker) {
            $time_now = time();
            foreach ($worker->connections as $connection) {
                if (empty($connection->lastMessageTime)) {
                    $connection->lastMessageTime = $time_now;
                    continue;
                }

                if ($time_now - $connection->lastMessageTime > HEARTBEAT_TIME) {
                    echo "Websocket connection close!\n" . time();
                    $connection->close();
                }
            }
        }
    );
};

$ws_worker->onMessage = function ($connection, $data) use ($ws_worker) {
    $user_infos = json_decode($data);
    $connection->lastMessageTime = time();
    $connection->name = $user_infos->name;

    Channel\Client::publish('broadcast', $data);
};

$ws_worker->onClose = function ($connection) {
    $quit = array(
        'type' => 'logout',
        'name' => $connection->name || 'Anonymous',
        'message' => $connection->name . ' left the chat!'
    );
    Channel\Client::publish('broadcast', json_encode($quit));
};

Worker::runAll();