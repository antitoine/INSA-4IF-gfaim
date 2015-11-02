<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
//header('Content-Type: application/json');

require_once 'vendor/flight/Flight.php';
require_once 'config/app.php';

foreach (glob("modules/*.php") as $filename)
{
    require_once $filename;
}

require_once 'config/route.php';

Flight::start();