<?php

namespace RGarman\Stats\Routes;

interface RouteInterface {
    public function ErrorHandle($Provided);
    public function __construct($URI = null, $Parameters = null, $Config = []);
    public function get($Parameters = null);
}