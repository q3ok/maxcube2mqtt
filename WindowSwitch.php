<?php

class WindowSwitch extends Device {
  
    protected $WindowOpen;
    
    public function isWindowOpen() {
        return $this->WindowOpen;
    }
    
    public function parseInfoData($info) { 
        if (!parent::parseInfoData($info)) return false;
        if ($info['mode'] == '00') {
            $this->WindowOpen = 0;
        } else {
            $this->WindowOpen = 1;
        }
        return true;

    }
    
    public function getPublishingItems() {
        return array_merge(parent::getPublishingItems(), array(
            'Open' => $this->WindowOpen,
        ));
    }
    
};
