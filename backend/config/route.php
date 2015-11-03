<?php

/******************** Routes Configuration ********************/

Flight::route('/', function(){
    echo 'API GFaim';
});

Flight::route('/test', 'test');

Flight::route('/search', function(){
    Flight::json(SearchEngineExtraction::getResultLinksOfQuery(Flight::request()->query['q']));
});

Flight::route('/annotate/test', function () {
    Flight::json(SearchEngineExtraction::getResultOfTestQuery());
});

Flight::route('/annotate', function () {
    Flight::json(TextAnnotation::annotate(Flight::request()->data['text']));
});

Flight::route('/extract', function () {
    Flight::json(TextExtractor::getAllText(SearchEngineExtraction::getResultOfTestQuery()
        ));
});

