# maxcube2mqtt
eQ-3 MAX! Cube to MQTT wrapper written in php

## Installation
```
php composer.phar install
```
## Configuration
```
cp config_example.php config.php
vi config.php
```

### Configuration options

`MAXCUBE_IP` IP Address of eQ-3 MAX! Cube

`MAXCUBE_PORT` set to 62910, as its unchangeable Cube listening port

`DEBUG` log a lot of spam to STDOUT

`MQTT_SERVER` IP Address of MQTT broker

`MQTT_PORT` listening port of MQTT broker

`MQTT_TOPIC` base topic of all messages from wrapper

`DATA_REQUEST_INTERVAL` interval (in seconds) how often informations about devices are requested from Cube

## Usage
```
php run.php
```

## Credits

### Protocol
https://github.com/Bouni/max-cube-protocol