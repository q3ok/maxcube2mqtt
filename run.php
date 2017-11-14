<?php

define( 'MAXCUBE_IP', '192.168.1.205' );
define( 'MAXCUBE_PORT', 62910 );
define( 'DEBUG', true );

require_once('Connection.php');
require_once('Maxparser.php');

$connection = Connection::getInstance();

echo 'Connecting to ' . MAXCUBE_IP . '...';
$connection->connect(MAXCUBE_IP, MAXCUBE_PORT);
echo 'OK' . PHP_EOL;

//for ($i=0;$i<7;$i++) {
for (;;) {
    
    $str = $connection->readMessage();
    Maxparser::parse($str);
    
    
}

$devices = Cube::getAllDevices();
foreach ($devices as $device) {
    print_r( $device->getRFAddress() );
    echo PHP_EOL;
}