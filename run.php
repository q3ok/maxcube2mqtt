<?php

require_once('vendor/autoload.php');

require_once('config.php');
require_once('Connection.php');
require_once('Maxparser.php');

$connection = Connection::getInstance();
echo 'Connecting to ' . MAXCUBE_IP . '...';
$connection->connect(MAXCUBE_IP, MAXCUBE_PORT);
echo 'OK' . PHP_EOL;

$mqttClient = new \Bluerhinos\phpMQTT(MQTT_SERVER, MQTT_PORT, 0);
$mqttClient->connect_auto();

$nextUpdate = time() + DATA_REQUEST_INTERVAL;
for (;;) {
    
    $str = $connection->readMessage();
    Maxparser::parse($str);
    
    $devices = Cube::getAllDevices();
    foreach ($devices as $device) {
        if ( $device->isUpdated() ) {
            $basetopic = Cube::getRoomById($device->getRoomId())->getName() . '/' . $device->getName();
            $pub_items = $device->getPublishingItems();
            
            foreach ($pub_items as $k => $i) {
                $mqttClient->publish( MQTT_TOPIC . $basetopic . '/' . $k, $i, 1);
            }
            $device->clearUpdated();
  
        }
    }
    unset ($devices);
    
    if (time() >= $nextUpdate) {
        // request update from cube
        if (DEBUG) {
            echo 'Sending l to Cube' . PHP_EOL;
            $connection->writeMessage("l:\r\n");
        }
            
        
        $nextUpdate = time() + DATA_REQUEST_INTERVAL;
    }
}

