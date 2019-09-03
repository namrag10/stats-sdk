<?php


use RGarman\Stats\Routes\Cache;
use RGarman\Stats\Routes\Route;
use RGarman\Stats\Client\ClientCore;


require "../vendor/autoload.php";


$Client = new ClientCore();

$Configs = [    // Change this to meet your credentials
    "Stats" => [
        "Username",
        "Password"
    ]
];


// Added Resources
$Client->AddResource("Entities", new Route("api/RU/configuration/entities", null, $Configs['Stats']));
$Client->AddResource("CacheInterface", new Cache());


$Client->Entities->getAndCache();

$Cache = $Client->CacheInterface->get("*");

