<?php
require_once('Utils.php');

// DuckDuckGo Test
$resultDDG = \Utils\Utils::CallAPI('GET', 'http://api.duckduckgo.com/', array(
    'q' => 'patate',
    'format' => 'json',
    'pretty' => 1
));

// Google Test

$resultGoogle = \Utils\Utils::CallAPI('GET', 'https://www.googleapis.com/customsearch/v1', array(
    'key' => 'AIzaSyASK1nR0p8Mjx4Fw9XHvMI-m20xSbtAwnc',
    'cx' => '014001075900266475386%3Aidgzvgxto1s',
    'q' => 'patate+recette'
));

//var_dump($resultDDG);
var_dump($resultGoogle);