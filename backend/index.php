<?php require 'autoload.php';

Flight::route('/', function(){
    echo 'API GFaim';
});

Flight::route('/test', 'test');

Flight::route('/search', function(){
    Flight::json(SearchEngineExtraction::getResultLinksOfQuery(Flight::request()->query['q']));
});

Flight::route('/textannotate/@text', function ($text) {
    Flight::json(TextAnnotation::annotate($text));
});

Flight::start();