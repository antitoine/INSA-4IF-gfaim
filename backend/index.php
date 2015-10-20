<?php
require_once('Utils.php');

$result = \Utils\Utils::CallAPI('GET', 'http://api.duckduckgo.com/', array(
    'q' => 'patate',
    'format' => 'json',
    'pretty' => 1
));
var_dump($result);