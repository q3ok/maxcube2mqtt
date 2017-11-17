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
        socket_set_option(self::$socket,SOL_SOCKET, SO_RCVTIMEO, array("sec"=>1, "usec"=>0));
        
        do {
            $sockStatus = socket_connect(self::$socket, $ip, $port);
        } while (!$sockStatus);

    }
    
    public static function readMessage() {
        socket_recv( self::$socket , $str, 2048, MSG_DONTWAIT );
        return $str;
    }
    
    public static function writeMessage($msg) {
        socket_write( self::$socket, $msg);
    }
    

    
    
};

