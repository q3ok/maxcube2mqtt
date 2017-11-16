<?php

require_once('config.php');
require_once('Connection.php');
require_once('MQTT.php');
require_once('Maxparser.php');

$connection = Connection::getInstance();
echo 'Connecting to ' . MAXCUBE_IP . '...';
$connection->connect(MAXCUBE_IP, MAXCUBE_PORT);
echo 'OK' . PHP_EOL;

$mqtt = new phpMQTT(MQTT_SERVER, MQTT_PORT, 0);
$mqtt->connect_auto();

$nextUpdate = time() + 5;
//for ($i=0;$i<13;$i++) {
for (;;) {
    
    $str = $connection->readMessage();
    Maxparser::parse($str);
    
    $devices = Cube::getAllDevices();
    foreach ($devices as $device) {
        if ( $device->isUpdated() ) {
            $basetopic = Cube::getRoomById($device->getRoomId())->getName() . '/' . $device->getName();
            $pub_items = $device->getPublishingItems();
            
            foreach ($pub_items as $k => $i) {
                $mqtt->publish( MQTT_TOPIC . $basetopic . '/' . $k, $i, 1);
            }
            $device->clearUpdated();
  
        }
    }
    unset ($devices);
    
    if (time() >= $nextUpdate) {
        // request update from cube
        if (DEBUG) {
            echo 'Sending l to Cube' . PHP_EOL;
        }
        Connection::writeMessage("l:\r\n");
        
        $nextUpdate = time() + 15;
    }
}
