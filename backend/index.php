<?php require 'autoload.php';

Flight::route('/', function(){
    echo 'It\'s Works !';
});

Flight::route('/module1', function(){
    Flight::json(getResultOfQuery(Flight::request()->data));
});

Flight::route('/test', 'test');

Flight::route('/search', 'test');

Flight::route('/module2/parse/@html', function($htmlString){
    parseHTML($htmlString);
});

//Flight::route('/module4', 'module_4');

Flight::route('/textannotate/@text', function ($text) {
    Flight::json(TextAnnotation::annotate($text));
});

Flight::start();