<?php

require_once('vendor/autoload.php');

require_once('config.php');
require_once('Connection.php');
require_once('Maxparser.php');

$connection = Connection::getInstance();
if (DEBUG) echo 'Connecting to ' . MAXCUBE_IP . '...';
$connection->connect(MAXCUBE_IP, MAXCUBE_PORT);
if (DEBUG) echo 'OK' . PHP_EOL;

$mqttClient = new \Bluerhinos\phpMQTT(MQTT_SERVER, MQTT_PORT, 0);
$mqttClient->connect();

define('MQTT_PING_INTERVAL', 5);

$nextUpdate = time() + DATA_REQUEST_INTERVAL;
$nextMQTTPing = time() + MQTT_PING_INTERVAL;

$lastDutyCycle = null;
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
    
    if ( Cube::getDutyCycle() != $lastDutyCycle && !is_null(Cube::getDutyCycle())  ) {
        $lastDutyCycle = Cube::getDutyCycle();
        $mqttClient->publish(MQTT_TOPIC . 'DutyCycle', $lastDutyCycle, 1);
    }
    
    if (time() >= $nextMQTTPing) {
        $mqttClient->ping();
        $nextMQTTPing = time() + MQTT_PING_INTERVAL;
    }
    
    if (time() >= $nextUpdate) {
        // request update from cube
        if (DEBUG) {
            echo 'Sending l to Cube' . PHP_EOL;
        }
        $connection->writeMessage("l:\r\n");
            
        $nextUpdate = time() + DATA_REQUEST_INTERVAL;
    }
    
}

