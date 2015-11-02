<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
//header('Content-Type: application/json');

require_once 'vendor/flight/Flight.php';

foreach (glob("config/*.php") as $filename)
{
    require_once $filename;
}

foreach (glob("*.php") as $filename)
{
    require_once $filename;
}
