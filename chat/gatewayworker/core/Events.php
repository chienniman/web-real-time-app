<?php
use \GatewayWorker\Lib\Gateway;

class Events
{
    public static function onConnect($client_id)
    {
        Gateway::sendToClient($client_id, json_encode(array(
            'type'      => 'init',
            'client_id' => $client_id
        )));
    }

    public static function onMessage($client_id, $message)
    {

    }
    public static function onClose($client_id)
    {
        $room_id = $_SESSION['room_id'];
        $uname   = $_SESSION['uname'];

        if (Gateway::getClientCountByGroup($room_id)) {
            Gateway::sendToGroup($room_id, json_encode(array(
                'type'      => 'close',
                'uname'     => $uname
            )));
        }   
    }
}