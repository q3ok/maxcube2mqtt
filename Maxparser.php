<?php
/**
 * Implementation based on
 * https://github.com/Bouni/max-cube-protocol
 */

require_once('Cube.php');

class Maxparser {
    
    private static $bufferM = null;
    
    private static function RFAddrParse(&$messageBin,&$currentPos) {
        $rfaddr = array(
            dechex($messageBin[++$currentPos]),
            dechex($messageBin[++$currentPos]),
            dechex($messageBin[++$currentPos]),
        );
        for ($j=0;$j<3;$j++) if (strlen($rfaddr[$j]) < 2) $rfaddr[$j] = '0' . $rfaddr[$j];
        return $rfaddr[0] . $rfaddr[1] . $rfaddr[2];
    }

    private static function parseH($message) {
        $parts = explode(',', $message);
        $return = array(
            'SerialNumber' => $parts[0],
            'RFAddress' => $parts[1],
            'FirmwareVersion' => $parts[2],
            'unknown' => $parts[3],
            'HTTPConnectionId' => $parts[4],
            'DutyCycle' => $parts[5],
            'FreeMemorySlots' => $parts[6],
            'CubeDate' => $parts[7],
            'CubeTime' => $parts[8],
            'StateCubeTime' => $parts[9],
            'NTPCounter' => $parts[10],
        );
        
        Cube::setData($return);
        
        if (DEBUG) {
            print_r($return);
            echo PHP_EOL;
        }
        
        return $return;
    }
    
    private static function parseM($message) {
        $parts = explode(',', $message);
        $indexM = intval($parts[0]);
        $countM = intval($parts[1]);
        $dataM = $parts[2];
        
        if ($indexM == 0) {
            self::$bufferM = null;
        }
        self::$bufferM .= $parts[2];
        if ($indexM == $countM-1) {
            return self::realParseM();
        }
        
    }
    
    private static function realParseM() {
        $message = base64_decode(self::$bufferM);
        $messageBin = unpack('C*', $message);
        
        // $message[x] == $messageBin[x+1]
        
        $return = array(
            'RoomCount' => (int)$messageBin[3],
        );
        
        /* load rooms data */
        $currentPos = 3;
        $rooms = array();
        for ($i=0;$i<$return['RoomCount'];$i++) {
            $rooms[$i] = array(
                'RoomId' => $messageBin[++$currentPos],
                'RoomnameLength' => $messageBin[++$currentPos],
            );            
            $rooms[$i]['Roomname'] = substr($message, $currentPos, $rooms[$i]['RoomnameLength']);                    
            $currentPos += $rooms[$i]['RoomnameLength'];
            $rooms[$i]['GroupRFAddress'] = self::RFAddrParse($messageBin, $currentPos);
        }
        $return['rooms'] = $rooms;
        foreach ($rooms as $room) {
            Cube::addRoom(new Room( $room['RoomId'], $room['Roomname'], $room['GroupRFAddress'] ));
        }
        
        /* load devices data */
        $return['DevicesCount'] = $messageBin[++$currentPos];
        $devices = array();
        for ($i=0;$i<$return['DevicesCount'];$i++) {
            $devices[$i]['DeviceType'] = $messageBin[++$currentPos];
            $devices[$i]['RFAddress'] = self::RFAddrParse($messageBin, $currentPos);
            $devices[$i]['SerialNumber'] = substr($message, $currentPos, 10);
            $currentPos += 10;
            $devices[$i]['DevicenameLength'] = $messageBin[++$currentPos];
            $devices[$i]['Devicename'] = substr($message,$currentPos,$devices[$i]['DevicenameLength']);
            $currentPos += $devices[$i]['DevicenameLength'];
            $devices[$i]['RoomId'] = $messageBin[++$currentPos];
        }
        $return['devices'] = $devices;
        foreach ($devices as $device) {
            switch ($device['DeviceType']) {
                case DEVICE_TYPE_THERMOSTATIC_HEAD:
                    $useDeviceClassName = 'ThermostaticHead';
                    break;
                
                case DEVICE_TYPE_SHUTTER:
                    $useDeviceClassName = 'WindowSwitch';
                    break;
                
                default:
                    $useDeviceClassName = 'Device';
                    break;
            }
            
            Cube::addDevice( new $useDeviceClassName(
                        $device['DeviceType'],
                        $device['RFAddress'],
                        $device['SerialNumber'],
                        $device['Devicename'],
                        $device['RoomId']) );
        }

        if (DEBUG) {
            print_r($return);
            echo PHP_EOL;
        }
        
        return $return;
    }
    
    private static function parseC($message) {
        $parts = explode(',', $message);
        $parts[1] = base64_decode($parts[1]);
        $messageBin = unpack('C*', $parts[1]);
        
        $device = Cube::getDeviceByRFAddress($parts[0]);
        if ($device === null) {
            if (DEBUG) echo 'Device ' . $parts[0] . ' not found!' . PHP_EOL;
            return;
        }
        
        $currentPos = 0;
        if (!is_object($device) && $device == DEVICE_TYPE_CUBE) {
            /* currently data doesnt needed at all */
            
        } else {
            /* basic information is same for all devices except cube */
            $info = array(
                'DataLength' => $messageBin[++$currentPos],
                'AddressOfDevice' => self::RFAddrParse($messageBin, $currentPos),
                'DeviceType' => $messageBin[++$currentPos],
                'RoomId' => $messageBin[++$currentPos],
                'FirmwareVersion' => $messageBin[++$currentPos],
                'TestResult' => $messageBin[++$currentPos],
                'SerialNumber' => substr($parts[1],$currentPos,10),
            );
            $currentPos+=10; // as of SerialNumber read
            
            switch ($device->getType()) {
                case DEVICE_TYPE_THERMOSTATIC_HEAD:
                    $device->setComfortTemperature( $messageBin[++$currentPos] / 2 );
                    $device->setEcoTemperature( $messageBin[++$currentPos] / 2 );
                    $device->setMaxSetPointTemperature( $messageBin[++$currentPos] / 2 );
                    $device->setMinPointTemperature( $messageBin[++$currentPos] / 2 );
                    $device->setTemperatureOffset( ($messageBin[++$currentPos] -3.5) /2 );
                    $device->setWindowOpenTemperature( $messageBin[++$currentPos] / 2 );
                    $device->setWindowOpenDuration( $messageBin[++$currentPos] / 5 );
                    $device->setBoost( $messageBin[++$currentPos] ); /* NOT */
                    $device->setDecalcification( $messageBin[++$currentPos] ); /* NOT */
                    $device->setMaxValve( $messageBin[++$currentPos] * 100/255 );
                    $device->setValveOffset( $messageBin[++$currentPos] * 100/255);
                    
                    /* and there goes weekly program
                     * but currently its not needed
                     */
                    
                    break;
                case DEVICE_TYPE_THERMOSTAT:
                    /* I dont have the wall thermostat, so I dont need this now :) */
                    break;
                
                
            }
            
        }
        
    }
    
    private static function parseL($message) {
        $message = base64_decode($message);
        $messageBin = unpack('C*', $message);
        
        $currentPos = 0;
        do {
            $submessageLength = $messageBin[++$currentPos];
            if (DEBUG) {
                echo 'L submessage detected, length: ' . $submessageLength . PHP_EOL;
            }
            
             $info = array(
                'rfAddr' => self::RFAddrParse($messageBin, $currentPos),
                'somethingNotKnown' => $messageBin[++$currentPos],
                'flags' => sprintf( "%08d", decbin($messageBin[++$currentPos])) . sprintf( "%08d", decbin($messageBin[++$currentPos])),
            );

            if ($submessageLength > 6) {
                $info = array_merge($info, array(
                    'valvePosition' => $messageBin[++$currentPos],
                    'temperature' => $messageBin[++$currentPos],
                    'dateUntil' => $messageBin[++$currentPos] . $messageBin[++$currentPos],
                    'timeUntil' => $messageBin[++$currentPos],
                ));
                if ($submessageLength > 11) {
                    $info = array_merge($info, array(
                        'actualTemperature' => $messageBin[++$currentPos] . $messageBin[++$currentPos],
                    ));
                }
            }
            $info['valid']=substr($info['flags'],3,1);
            $info['error']=substr($info['flags'],4,1);
            $info['isAsnwer']=substr($info['flags'],5,1);
            $info['statusInitialized']=substr($info['flags'],6,1);
            $info['batteryLow']=substr($info['flags'],8,1);
            $info['linkFault']=substr($info['flags'],9,1);
            $info['panelLocked']=substr($info['flags'],10,1);
            $info['gatewayKnown']=substr($info['flags'],11,1);
            $info['dstEnabled']=substr($info['flags'],12,1);
            $info['mode']=substr($info['flags'],14,2);
            
            $device = Cube::getDeviceByRFAddress($info['rfAddr']);
            if (is_object($device)) {
                $device->parseInfoData( $info );
            } else {
                if (DEBUG) {
                    echo "L messages only for devices supported". PHP_EOL;
                }
            }
            unset ($device);
            
        } while ( isset($messageBin[$currentPos+1]) && ($messageBin[$currentPos+1] != 206 && $messageBin[$currentPos+2] != 0));
        /* protocol described by Bouni assumes that L message is always terminated with 0xce and 0x00
         * but for me it looks like the message doesnt have the terminator
         * probably it depends on Cube firmware version, but I am not sure :)
         */
    }
    
    public static function parse($message) {
        $message = explode( ':', trim($message) );
        if ( strlen($message[0]) != 1 ) return;

        if ( method_exists( 'Maxparser', 'parse' . $message[0] ) ) {
            call_user_func(array('Maxparser','parse' . $message[0]), $message[1]);
        } else {
            if (DEBUG) echo 'No parser for ' . $message[0] . ' message' . PHP_EOL;
        }
    }
    
}