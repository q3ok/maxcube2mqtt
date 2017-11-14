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
    
};

