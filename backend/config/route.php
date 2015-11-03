<?php

/******************** Routes Configuration ********************/

Flight::route('/', function(){
    echo 'API GFaim';
});

Flight::route('/test', 'test');

Flight::route('/search', function(){
    Flight::json(
        TextAnnotation::annotateTexts(
            TextExtractor::getAllText(
                SearchEngineExtraction::getResultLinksOfQuery(
                    Flight::request()->query['q']
                )
            )
        )
    );
});

Flight::route('/search/test', function(){
    Flight::json(SearchEngineExtraction::getResultLinksOfQuery(Flight::request()->query['q']));
});

Flight::route('/annotate/test', function () {
    Flight::json(TextAnnotation::annotateTextsTest());
});

Flight::route('/extract/test', function () {
    Flight::json(TextExtractor::getAllTextTest());
});

Flight::route('/enhance/test', function () {
    Flight::json(ResultEnhancer::TreatTest());
});

