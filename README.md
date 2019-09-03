# Stats.com SDK

## About

The `sdk-stats` package is an Software Development Kit for various stats.com API endpoints.
The way this SDK works is by the user customising the endpoints to use and assign their own names to them for easier and more efficient experience for the developer

## Installation

Require the `rgarman/sdk-stats` package in your `composer.json` and update your dependencies:

```bash
composer require rgarman/sdk-stats
```

## Creating a Client:
I would recommend that an assoc array containing your login credentials is created, for ease later. This also will aid in creating multiple different instances with different API's simultaniously
Create an instance of the ClientCore() class

```php
use RGarman\Stats\statsClient;

// A config of ["{your username}", "{your password}"] would work too!
$Configs = [
    "Stats" => [
        "{USERNAME}",
        "{PASSWORD}"
    ],
    ...
]

$StatsClient = new ClientCore();
```

## Adding Resources
To add a Resource to use, simply call the AddResource() method from the ClientCore and pass an instance of either a Route, or Cache<br>
- - - 
The parameters to which are: the name, and the chosen instance
Route parameters:
<ol>
<li>Endpoint (String)</li>
<li>Parameters (Array)</li>
<li>Config Credentials</li>
</ol>

```php
// Generate a new endpoint resource
$StatsClient->AddResource("SquadList", new Route("api/RU/clubSquads/", ["Competition ID", "Season ID", "Team ID"], $Configs["Stats"]));

//Generate a new Cache interface
$StatsClient->AddResource("Caching", new Cache());

```


## Using the Resources

Using the name you assigned the resource, simply use it as a property and use any of the below listed functions

```php

//This outputs the Squad List for England in the Six Nations 2019 and generates a cache containing the response from the API
var_dump($StatsClient->SquadList->getAndCache([301, 2019, 1114]));


//Using the "Caching" resource (previously created), we can see all of the cached data using:
var_dump($StatsClient->Caching->get("*"));

//The name of the above cache is called "api-RU-clubSquads-301-2019-1114", so to get this data, we use the following:
var_dump($StatsClient->Caching->get("api-RU-clubSquads-301-2019-1114"));

```
# Function List
## Route Instance:

#### get(  $Parameters (Array/String)  )
##### makes a call to the current API endpoint selected using the Parameters provided as an array or string<br>Returns the response from the API as an array

#### getAndCache (  $Parameters (Array/String)  )
##### Basically the same functionality as __get__, except for the fact that a cache will be createdin the form of raw `JSON`<br>Returns the response from the API as an array (In addition to the cache)

#### setMethod( $Method (String)  )
##### Sets the type of HTTP protocol to use e.g. GET, POST, PUT etc. Accepts all 7 types

#### setBase(  $Base (String)  )
##### Sets the Base URI of the API to use, so other API's can be used within the same SDK!

- -

## Cache Instance:

#### get(  $Cache (String)  )
##### Retreives the given cache, use `"*"` to get the names of all stored cached data

- - -
See the Example for more