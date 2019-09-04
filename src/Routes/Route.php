<?php

namespace RGarman\Stats\Routes;

use Exception;
use GuzzleHttp\Client;
use RGarman\Stats\Routes\RouteInterface;

class Route implements RouteInterface {
	
	protected $RequiredParameters;
	protected $GuzzleClient;
	protected $URI;
	protected $Parameters;
	protected $Config;
	protected $BaseURI = "http://rugbyunion-api.stats.com/";
	protected $Method = "GET";
	protected $Mode = "API";
	protected $CachePath = __DIR__ . "/../../../../../Cache";

	public function setMethod($Method){
		$this->Method = strtoupper($Method);
		return $this;
	}
	

	public function setBase($Base){
		$this->BaseURI = $Base;
		return $this;
	}

	public function setCachePath($Path){
		$this->CachePath = $Path;
		return $this;
	}
	
	
	public function getAndCache($Parameters = null){
		$Response = $this->get($Parameters);
		if($Response == False){
			return false;
		}
		if($Parameters == null){
			$Add = "";
		}else{
			$Add = (is_array($Parameters)) ? join("-", $Parameters) : str_replace("/", "-", $Parameters);
		}

		$Add = str_replace("/", "-", $this->URI) . "-" . $Add;

		file_put_contents($this->CachePath . "/" . $Add . ".json", json_encode($Response));
		return $Response;
	}


	public function __construct($URI = null, $Parameters = [], $Config = []){	

		try{
			if(is_null($URI)) throw new Exception("URI Cannot be null!");
		}catch(Exception $e){
			return $e->getMessage();
		}

		$this->RequiredParameters = (is_array($Parameters)) ? sizeof($Parameters) : 0;
		$this->Parameters = $Parameters;
		$this->GuzzleClient = new Client();
		$this->URI = $URI;

		$this->BaseURI = (isset($Config['Base'])) ? $Config['Base'] : $this->BaseURI;
		

		$this->Config['auth'] = $Config;

		if(!file_exists($this->CachePath . "/")){
			mkdir($this->CachePath);
		}
	}


	public function ErrorHandle($Provided){
		return "\n\n** This endpoint requires {$this->RequiredParameters} parameters:\n" . join($this->Parameters, ", ") . "\n{$Provided} Provided **\n\n";
	}


	public function get($Parameters = null){
		
		if(empty($this->URI)) return "No endpoint set";

		if($Parameters == null){
			$Add = "";
		}else{
			$Add = (is_array($Parameters)) ? join("/", $Parameters) : strval($Parameters);
		}

		$Ref = ($this->RequiredParameters == 0) ? 0 : $this->RequiredParameters -1;
		

		try{
			if(substr_count($Add, "/") === $Ref){
				$response = $this->GuzzleClient->request($this->Method, $this->BaseURI . $this->URI . $Add, $this->Config);
				return json_decode($response->getBody(), true);
			}else{
				throw new Exception ($this->ErrorHandle(substr_count($Add, "/") + 1));
			}
		}catch(Exception $e){
			return $e->getMessage();
		}
	}

}