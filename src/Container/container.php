<?php

namespace RGarman\Stats\Container;

use Exception;

class container {
    protected $Container = [];

    public function get($Key){
        if($Key == "*"){
            return array_keys($this->Container);
        }
        if(isset($this->Container)){
            return $this->Container[$Key];
        }
        
        throw new Exception("No Such Key!");
    }

    public function set($Key, $Value){
        if($Key == "*"){
            throw new Exception("Cannot set special key");
        }
        $this->Container[$Key] = $Value;
    }

    public function has($Key){
        return isset($this->Container[$Key]);
    }

}