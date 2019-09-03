<?php

namespace RGarman\Stats\Client;

use Exception;
use RGarman\Stats\Container\container;
use RGarman\Stats\Routes\RouteInterface;

class ClientCore {

	protected $Container;

    public function __construct(){
		$this->Container = new container();
	}




	public function AddResource($Name, RouteInterface $Route){

		$this->Container->set(strtolower($Name), $Route);
		return true;
	}
	



	public function __get($method){
		try{
			$method = strtolower($method);
			if($this->Container->has($method)){
				return $this->Container->get($method);
			}

			throw new Exception("Unknown Endpoint '{$method}'");

		}catch(Exception $e){
			echo $e->getMessage() . " in " . $e->getTrace()[0]['file'] . ":" . $e->getTrace()[0]['line'] . "\n";
		}
	}

	


	public function getResources(){
		$Resources = $this->Container->get("*");
		return $Resources;
	}



	
	public function Help($Chosen = ""){
		while(true){
			os.system("clear");
			echo "*********\n*Welcome*\n*********\n\n";

			if($Chosen != ""){
				echo $Chosen . "\n" . str_repeat("-", strlen($Chosen)) . "\n\n";
				$Msg;
				switch($Chosen){
					case "addresource":
						$Msg = "This method makes another resource available to the user. Syntax:\n ->addResource({Name (String)}, {Endpoint (String)}, {Parameters (Array)})";	
						break;
					case "call":
						$Msg = "This method calls the stats.com API with the given parameters, the resources must firt be selected, or an error will occur. SYNTAX:\n ->call({Parameters (String OR Array)})";	
						break;
					case "callthis":
						$Msg = "Much like the call method, except, this takes a full URL, the API address, and the endpoint in one. EXAMPLE:\nhttp://rugbyunion-api.stats.com/api/RU/clubSquads/241/2018";	
						break;
					case "getresources":
						$Msg = "Returns a list of all available resources in the form of an array";	
						break;
					case "help":
						$Msg = "Manual for this SDK";	
						break;
					case "setbase":
						$Msg = "Sets the Base URI for the session. SYNTAX\n ->setBase({BASE URI (String)})";	
						break;
					case "setmethod":
						$Msg = "Sets the method to use, accepts all 7 HTTP protocols as string. SYNTAX:\n ->setMethod({HTTP Protocol (String)})";	
						break;
					default:
						$Msg = "Not Found";
						break;
				}

				echo $Msg;
				$Chosen = "";
				
			}else{
				$Command = readline("Which Command? => ");
				if($Command == "exit" || $Command == "quit" || $Command == "close"){
					break;
				}

				

				#GET COMMANDS
				$Cmds = get_class_methods("RGarman\\Stats\\Client\\ClientCore");


				$RouteCmds = get_class_methods("RGarman\\Stats\\Routes\\Route");
				foreach($RouteCmds as $value){
					array_push($Cmds, $value);
				}

				$CacheCmds = get_class_methods("RGarman\\Stats\\Routes\\Cache");
				foreach($CacheCmds as $value){
					array_push($Cmds, $value);
				}


				$Commands = array_filter(array_map(function($e){
					$Ignore = ["__get", "__construct"];
					if(array_search($e, $Ignore) === false){
						return strtolower($e);
					}
				}, $Cmds), function($ele){
					return isset($ele);
				});
				sort($Commands);
				#END GET COMMANDS

				var_dump($Commands);
				die();

				#DO YOU MEAN?
				if(array_search(strtolower($Command), $Commands)){
					$this->Help($Command);
				}else{
					$DoYouMean = [];
					foreach($Commands as $value){
						similar_text($value, $Command, $percentage);
						if($percentage > 40){
							array_push($DoYouMean, $value);
						}
					}
					if(sizeof($DoYouMean) > 0){
						echo "\nDo You Mean:\n";
						foreach($DoYouMean as $name => $Poss){
							echo "\t" . $name . ") " . $Poss . "\n";
						}
						echo "\nEnter Number of command, or anything else to skip\n";
						$CommandIndex = readline(">");
						if($CommandIndex != ""){
							$CommandIndex = intval($CommandIndex);
						}
						if(isset($DoYouMean[$CommandIndex])){
							$this->Help($DoYouMean[$CommandIndex]);
						}

					}else{
						echo "No Command Found :(";
					}
				}
				#END DO YOU MEAN
			}
			echo "\n\n";
			readline("Back");
		}
		return true;
	}
}