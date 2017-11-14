<?php

require_once('Room.php');
require_once('ThermostaticHead.php');

define( 'DEVICE_TYPE_CUBE', 0 );

class Cube {

    private static $SN;
    private static $RFAddr;
    private static $FirmwareVersion;
    private static $HTTPConnectionId;
    private static $DutyCycle;
    private static $FreeMemorySlots;
    private static $CubeDate;
    private static $CubeTime;
    private static $StateCubeTime;
    private static $NTPCounter;
    
    /**
     * @var Room[]
     */
    private static $Rooms = array();
    /**
     * @var Device[]
     */
    private static $Devices = array();
    
    public static function setData($dataArray) {
        
        self::$SN = $dataArray['SerialNumber'];
        self::$RFAddr = $dataArray['RFAddress'];
        self::$FirmwareVersion = $dataArray['FirmwareVersion'];
        self::$HTTPConnectionId = $dataArray['HTTPConnectionId'];
        self::$DutyCycle = $dataArray['DutyCycle'];
        self::$FreeMemorySlots = $dataArray['FreeMemorySlots'];
        self::$CubeDate = $dataArray['CubeDate'];
        self::$CubeTime = $dataArray['CubeTime'];
        self::$StateCubeTime = $dataArray['StateCubeTime'];
        self::$NTPCounter = $dataArray['NTPCounter'];
        
    }
    
    public static function getDutyCycle() {
        return self::$DutyCycle;
    }
    
    public static function addRoom(Room $room) {
        self::$Rooms[] = $room;
    }
    
    public static function addDevice(Device $device) {
        self::$Devices[] = $device;
    }
    
    /**
     * @return Room[]
     */
    public static function getAllRooms() {
        return self::$Rooms;
    }
    
    /**
     * @return Device[]
     */
    public static function getAllDevices() {
        return self::$Devices;
    }
    
    /**
     * @param string $rfaddr
     * @return Device
     */
    public static function getDeviceByRFAddress($rfaddr) {
        if ($rfaddr == self::$RFAddr) {
            return DEVICE_TYPE_CUBE; /* it should not be done like that */
        }
        foreach (self::$Devices as &$device) {
            if ($device->getRFAddress() == $rfaddr) {
                return $device;
            }
        }
        return null;
    }
    
};


