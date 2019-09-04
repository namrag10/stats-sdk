<?php

namespace RGarman\Stats\Client;

use Exception;
use RGarman\Stats\Routes\Route;
use RGarman\Stats\Container\container;
use RGarman\Stats\Routes\RouteInterface;

class ClientCore {

	protected $Container;
	private $Ignore = ["__get", "__construct", "ErrorHandle"];

    public function __construct(){
		$this->Container = new container();
	}




	public function AddResource($Name, RouteInterface $Route){
		$this->Container->set(strtolower($Name), $Route);
		return $this;
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


	public function addBaseResources($Config){
		$this->AddResource("Entities", new Route("api/RU/configuration/entities", [], $Config));
		$this->AddResource("SeasonList", new Route("api/RU/competitions/seasons/", ["CompetitionID", "SeasonID"], $Config));
		$this->AddResource("FixtureList", new Route("api/RU/competitions/fixtures/", ["CompetitionID", "SeasonID", "TeamID"], $Config));
		$this->AddResource("TeamStats", new Route("api/RU/teamStats/", ["CompetitionID", "SeasonID", "TeamID"], $Config));
		$this->AddResource("TeamSnapShot", new Route("api/RU/teamStatsSnapshot/", ["CompetitionID", "SeasonID", "TeamID"], $Config));
		$this->AddResource("PlayerStats", new Route("api/RU/playerStats/", ["CompetitionID", "SeasonID", "PlayerID"], $Config));
		$this->AddResource("PlayerSnapShot", new Route("api/RU/playerStatsSnapshot/", ["CompetitionID", "SeasonID", "PlayerID"], $Config));
		$this->AddResource("PlayerBio", new Route("api/RU/playerProfiles/", ["CompetitionID", "SeasonID", "PlayerID"], $Config));
		$this->AddResource("SquadList", new Route("api/RU/clubSquads/", ["CompetitionID", "SeasonID", "TeamID"], $Config));
		$this->AddResource("MatchData", new Route("api/RU/matchStats/", ["MatchID"], $Config));
		$this->AddResource("EventFlow", new Route("api/RU/matchstats/eventsFlow/", ["MatchID"], $Config));
		$this->AddResource("FormGuide", new Route("api/RU/matchStats/FormGuide/", ["MatchID"], $Config));
	}

	
	public function Help(){

		#GET COMMANDS
		$Cmds = get_class_methods("RGarman\\Stats\\Client\\ClientCore");

		$RouteCmds = get_class_methods("RGarman\\Stats\\Routes\\Route");
		foreach($RouteCmds as $value){
			if(array_search($value, $this->Ignore) === false){
				array_push($Cmds, $value);
			}
		}

		$CacheCmds = get_class_methods("RGarman\\Stats\\Routes\\Cache");
		foreach($CacheCmds as $value){
			if(array_search($value, $this->Ignore) === false){
				array_push($Cmds, $value . " - Cache");
			}
		}

		$Commands = array_filter(array_map(function($e){					
			if(array_search($e, $this->Ignore) === false){
				return strtolower($e);
			}
		}, $Cmds), function($ele){
			return isset($ele);
		});
		sort($Commands);
		#END GET COMMANDS

		#--------------------------------------

		while(true){
			$Chosen = "";
			os.system("clear");
			echo "*********\n*Welcome*\n*********\n\n";

			$Command = readline("Which Command? => ");
			if($Command == "exit" || $Command == "quit" || $Command == "close"){
				break;
			}

			
			#DO YOU MEAN?
			if(array_search(strtolower($Command), $Commands)){
				$Chosen = $Command;
			}elseif($Command == "*"){

				echo "\nAll Available Commands:\n";
				foreach($Commands as $name => $Poss){
					echo "\t" . $name . ") " . $Poss . "\n";
				}
				echo "\nEnter Number of command, or anything else to skip\n";
				$CommandIndex = readline(">");
				if($CommandIndex != ""){
					$CommandIndex = intval($CommandIndex);
				}
				if(isset($Commands[$CommandIndex])){
					$Chosen = $Commands[$CommandIndex];
				}
				
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
						$Chosen = $DoYouMean[$CommandIndex];
					}
				}else{
					echo "No Command Found :(";
				}
			}
			#END DO YOU MEAN

			if($Chosen != ""){
				os.system("clear");
				echo "*********\n*Welcome*\n*********\n\n";

				echo $Chosen . "\n" . str_repeat("-", strlen($Chosen)) . "\n\n";
				$Msg;
				switch($Chosen){
					case "addresource":
						$Msg = "This method makes another resource available to the user. Syntax:\n ->addResource({Name (String)}, {Endpoint (String)}, {Parameters (Array)})";	
						break;
					case "get - cache":
						$Msg = "This method allows you to access the cached data, it takes 1 parameter which is the name of the cached data you wish to access, or the '*' wildcard can be used which will return all available cached data keys. SYNTAX:\n ->get({Parameter} ('String'))";	
						break;
					case "get":
						$Msg = "This method calls the stats.com API with the given parameters, the resources must first be selected, or an error will occur. SYNTAX:\n ->get({Parameters (String OR Array)})";	
						break;
					case "getandcache":
					$Msg = "This method calls the stats.com API with the given parameters, the resources must firt be selected, or an error will occur. In addition, a cache of the response will be generated and can be accessed using a cache interface instance of client SYNTAX:\n ->getAndCache({Parameters (String OR Array)})";	
						break;
					case "getresources":
						$Msg = "Returns a list of all available resources in the form of an array";		
						break;
					case "help":
						$Msg = "Manual for this SDK";
						break;
					case "setbase":
						$Msg = "Sets the Base URI for the session. SYNTAX:\n ->setBase({BASE URI (String)})";
						break;
					case "setcachepath":
						$Msg = "Over write the default path to which the cached data will be saved SYNTAX:\n ->setCachePath({NewPath} (String))";	
						break;
					case "setmethod":
						$Msg = "Sets the method to use, accepts all 7 HTTP protocols as string. SYNTAX:\n ->setMethod({HTTP Protocol (String)})";	
						break;
					case "addbaseresources":
						$Msg = "Adds 12 base resources that I think are relivant to most projects, view them using the getResources() method. SYNTAX:\n ->addBaseResources({Config} (Array))";
						break;
					default:
						$Msg = "No Information about {$Chosen}";
						break;
				}

				echo $Msg;
			}
			echo "\n\n";
			readline("<Press Enter>");

		}
		

		
		return true;
	}
}