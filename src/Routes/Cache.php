<?php

namespace RGarman\Stats\Routes;

use RGarman\Stats\Routes\RouteInterface;

class Cache implements RouteInterface {

    protected $URI;



    
    public function __construct($URI = __DIR__ . "/../../../../../Cache", $Parameters = null, $Config = []){
		$this->URI = $URI;
		
		if(!file_exists($this->URI . "/")){
			mkdir($this->URI);
		}

    }
    



    public function get($Parameters = null){
		if($Parameters == "*"){
			$ret = array_filter(	
				array_map(function($e){
					return str_replace(".json", "", $e);
				}, scandir($this->URI . "/")),
				
				function($e){
					if($e == "." || $e == ".."){
						return false;
					}
					return true;
				}
			);
			sort($ret);

			return $ret;
		}
	
		if(array_search($Parameters . ".json", scandir($this->URI)) !== False){
			return json_decode(file_get_contents($this->URI . $Parameters . ".json"), true);
		}
		return "No Cache Found for {$Parameters}!";
    }
    



    public function ErrorHandle($Provided){
		return "\n\n** This endpoint requires {$this->RequiredParameters} parameters:\n" . join($this->Parameters, ", ") . "\n{$Provided} Provided **\n\n";
	}
}