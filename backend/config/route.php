<?php

/******************** Routes Configuration ********************/

Flight::route('/', function(){
    echo 'API GFaim';
});

Flight::route('/test', 'test');

Flight::route('/search', function(){
    
    $annotatedUrls = TextAnnotation::annotateTexts(
                        TextExtractor::getAllText(
                            SearchEngineExtraction::getResultLinksOfQuery(
                                Flight::request()->query['q']
                            )
                        )
                    );
                    
    $enhancedResults = ResultEnhancer::Process($annotatedUrls);
    
    $connectedComponents = GraphSimilarity::getConnectedComponentsJSON($enhancedResults, 0.5);
    
    //echo '<pre>';
    //var_dump($connectedComponents);
    //echo '</pre>';

    Flight::json($connectedComponents);
});

Flight::route('/search/test', function(){
    //Flight::json(SearchEngineExtraction::getResultLinksOfQuery(Flight::request()->query['q']));
    
    echo '<pre>';
    var_dump(SearchEngineExtraction::getResultLinksOfQuery(Flight::request()->query['q']));
    echo '</pre>';
});

Flight::route('/annotate/test', function () {
    Flight::json(TextAnnotation::annotateTextsTest());
});

Flight::route('/extract/test', function () {
    Flight::json(TextExtractor::getAllTextTest());
});

Flight::route('/enhance/test', function () {
    Flight::json(ResultEnhancer::ProcessTest());
});

Flight::route('/similarity/test', function () {
    echo '<pre>';
    var_dump(GraphSimilarity::getConnectedComponentsJSONTest());
    echo '</pre>';
});
