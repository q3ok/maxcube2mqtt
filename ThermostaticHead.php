<?php

require_once('Device.php');

class ThermostaticHead extends Device {
    protected $ComfortTemperature;
    protected $EcoTemperature;
    protected $MaxSetPointTemperature;
    protected $MinSetPointTemperature;
    protected $TemperatureOffset;
    protected $WindowOpenTemperature;
    protected $WindowOpenDuration;
    protected $Boost;
    protected $Decalcification;
    protected $MaxValveSetting;
    protected $ValveOffset;
    protected $WeeklyProgram;
    protected $ValvePosition;
    protected $ActualTemperature;
    protected $Mode;
    
    public function setComfortTemperature($temp) {
        $this->ComfortTemperature = $temp;
    }
    
    public function setEcoTemperature($temp) {
        $this->EcoTemperature = $temp;
    }
    
    public function setMaxSetPointTemperature($temp) {
        $this->MaxSetPointTemperature = $temp;
    }
    
    public function setMinPointTemperature($temp) {
        $this->MinSetPointTemperature = $temp;
    }
    
    public function setTemperatureOffset($offset) {
        $this->TemperatureOffset = $offset;
    }
    
    public function setWindowOpenTemperature($temp) {
        $this->WindowOpenTemperature = $temp;
    }
    
    public function setWindowOpenDuration($time) {
        $this->WindowOpenDuration = $time;
    }
    
    public function setBoost($boost) {
        $this->Boost = $boost;
    }
    
    public function setDecalcification($decal) {
        $this->Decalcification = $decal;
    }
    
    public function setMaxValve($maxv) {
        $this->MaxValveSetting = $maxv;
    }
    
    public function setValveOffset($offset) {
        $this->ValveOffset = $offset;
    }
    
    public function getValvePosition() {
        return $this->ValvePosition;
    }
    
    public function parseInfoData($info) { 
        if (!parent::parseInfoData($info)) return false;
        $this->ValvePosition = $info['valvePosition'];
        if (isset($info['actualTemperature'])) {
            $this->ActualTemperature = $info['actualTemperature'];
        }
        switch ($info['mode']) {
            case 00:
                $this->Mode = 'Auto';
                break;
            case 01:
                $this->Mode = 'Manual';
                break;
            case 10:
                $this->Mode = 'Vacation';
                break;
            default:
                $this->Mode = 'Boost';
        }
        
    }
    
    public function getPublishingItems() {
        $publishing_items = array(
            'ComfortTemperature',
            'EcoTemperature',
            'ValvePosition',
            'ActualTemperature',
            'Mode',
        );
        $return = parent::getPublishingItems();
        foreach ($publishing_items as $item) {
            if (isset($this->$item)) {
                $return[$item] = $this->$item;
            }
        } 
        return $return;
    }
    
};

