<?php

class Room {
  
    private $Id;
    private $Name;
    private $GroupRFAddress;
    
    public function __construct($id, $name, $grouprfaddr) {
        $this->Id = $id;
        $this->Name = $name;
        $this->GroupRFAddress = $grouprfaddr;
    }
    
    public function getId() {
        return $this->Id;   
    }
    public function getName() {
        return $this->Name;
    }
    public function getGroupRFAddress() {
        return $this->GroupRFAddress;
    }
    
};
