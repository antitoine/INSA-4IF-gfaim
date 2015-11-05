<?php

/******************** Routes Configuration ********************/

/**
 * Route to present and test all modules
 */
Flight::route('/', function() {
    include('home.php');
});

/**
 * The main route : GFaim Search Engine
 */
Flight::route('/search', function(){
    $result = array();
    
    if (isset(Flight::request()->query['q']))
    {
        $confidence = SPOTLIGHT_DEFAULT_CONFIDENCE;
        $similarity = GRAPH_DEFAULT_SIMILARITY;
        $query = Flight::request()->query['q'];

        if (isset(Flight::request()->query['confidence']))
        {
            $confidence = Flight::request()->query['confidence'];
        }

        if (isset(Flight::request()->query['similarity'])) 
        {
            $similarity = Flight::request()->query['similarity'];
        }

        $result = GFaimSearchEngine::search($query, $confidence, $similarity);
    }
    Flight::json($result);
});

/**
 * Route to test the module 1
 */
Flight::route('/search/test', function(){
    $result = array();
    
    if (isset(Flight::request()->query['q']))
    {
        $query = Flight::request()->query['q'];
        $result = SearchEngineExtraction::getResultLinksOfQuery($query);
    }
    Flight::json($result);
});

/**
 * Route to test the module 2
 */
Flight::route('/extract/test', function () {
    $result = TextExtractor::getAllTextTest();
    Flight::json($result);
});

/**
 * Route to test the module 1 and 2
 */
Flight::route('/search/and/extract/test', function () {
    $result = array();
    
    if (isset(Flight::request()->query['q']))
    {
        $query = Flight::request()->query['q'];
        $resultOfModule1 = SearchEngineExtraction::getResultLinksOfQuery($query);
        $allUrl = array_keys($resultOfModule1);
        $result = TextExtractor::getAllText($allUrl);
    }
    Flight::json($result);
});

/**
 * Route to test the module 3
 */
Flight::route('/annotate/test', function () {
    Flight::json(TextAnnotation::annotateTextsTest());
});

/**
 * Route to test the module 4
 */
Flight::route('/enhance/test', function () {
    Flight::json(ResultEnhancer::ProcessTest());
});

Flight::route('/enhance/dataConcept/test', function () {
    Flight::json(ResultEnhancer::ProcessTestDataConcept());
});

Flight::route('/similarity/test', function () {
    echo '<pre>';
    var_dump(GraphSimilarity::getConnectedComponentsJSONTest());
    echo '</pre>';
});

Flight::route('/gfaim/test', function () {
    Flight::json(GFaimSearchEngine::search(Flight::request()->query['q']));
});
