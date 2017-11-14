<?php

class Connection {
    private static $socket;
    
    protected static $instance;
    protected function __construct() {}
    protected function __clone() {}
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Connection;
        }
        return self::$instance;
    }
    
    public static function connect($ip, $port) {
        self::$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        do {
            $sockStatus = socket_connect(self::$socket, $ip, $port);
        } while (!$sockStatus);
        
    }
    
    public static function readMessage() {
        $str = socket_read(self::$socket, 2048, PHP_NORMAL_READ);
        return $str;
    }
    

    
    
};

