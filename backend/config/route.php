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
    $result = array();
    
    if (isset(Flight::request()->query['url']))
    {
        $url = Flight::request()->query['url'];
        $result = TextExtractor::getAllText(array($url));
    }
    Flight::json($result);
});

/**
 * Route to test the module 1 and 2
 */
Flight::route('/search/extract/test', function () {
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
    $result = array();
    
    if (isset(Flight::request()->query['text']))
    {
        $text = Flight::request()->query['text'];
        $confidence = 1;
        if (isset(Flight::request()->query['confidence']))
        {
            $confidence = Flight::request()->query['confidence'];
        }
        $result = TextAnnotation::annotate($text, $confidence);
    }
    Flight::json($result);
});

/**
 * Route to test the module 1, 2 and 3
 */
Flight::route('/search/extract/annotate/test', function () {
    $result = array();
    
    if (isset(Flight::request()->query['q']))
    {
        $query = Flight::request()->query['q'];
        $confidence = 1;
        if (isset(Flight::request()->query['confidence']))
        {
            $confidence = Flight::request()->query['confidence'];
        }
        $resultOfModule1 = SearchEngineExtraction::getResultLinksOfQuery($query);
        $allUrl = array_keys($resultOfModule1);
        $resultOfModule2 = TextExtractor::getAllText($allUrl);
        $result = TextAnnotation::annotateTexts($resultOfModule2, $confidence);
    }
    Flight::json($result);
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
    Flight::json(GraphSimilarity::getConnectedComponentsJSONTest());
});

Flight::route('/gfaim/test', function () {
    Flight::json(GFaimSearchEngine::search(Flight::request()->query['q']));
});
