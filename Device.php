<?php

define( 'DEVICE_TYPE_THERMOSTATIC_HEAD', 1 );
define( 'DEVICE_TYPE_THERMOSTATIC_HEAD_PLUS', 2);
define( 'DEVICE_TYPE_THERMOSTAT', 3);
define( 'DEVICE_TYPE_SHUTTER', 4);
define( 'DEVICE_TYPE_BUTTON', 5);

class Device {
    protected $Type;
    protected $RFAddress;
    protected $SN;
    protected $Name;
    protected $RoomId;
    
    public function __construct($type, $rfaddr, $sn, $name, $roomid) {
        $this->Type = $type;
        $this->RFAddress = $rfaddr;
        $this->SN = $sn;
        $this->Name = $name;
        $this->RoomId = $roomid;
    }
    
    public function getType() {
        return $this->Type;
    }
    public function getRFAddress() {
        return $this->RFAddress;
    }
    public function getSN() {
        return $this->SN;
    }
    public function getName() {
        return $this->Name;
    }
    public function getRoomId() {
        return $this->RoomId;
    }
    
    public function setRoomId($room_id) {
        $this->RoomId = $room_id;
    }
};
