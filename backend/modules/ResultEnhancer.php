<?php

class ResultEnhancer {
    
    // --------------------------------- CONFIG --------------------------------
    
    const DEBUG = TRUE;
    const REQUEST_OUTPUT_FORMAT = "json"; // Can be xml, json, text-turtle ... 
    const TIMEOUT = 30000;
    const GRAPH_URI = "http://dbpedia.org";
    const RESULT_ONLY = TRUE;
    
    // -------------------------------------------------------------------------
    
    private static function buildHTTPRequest($sparqlQuery) {
        return "http://dbpedia.org/sparql?".
                "default-graph-uri=".urldecode(self::GRAPH_URI).
                "&query=" . urlencode($sparqlQuery) . 
                "&format=".self::REQUEST_OUTPUT_FORMAT.
                "&timeout=".self::TIMEOUT;
    }
    
    // Fonction creation de requete 
    private static function getTriplesLinkedToURI($uri) {
        return "SELECT ?s ?p ?o WHERE { ?s ?p ?o.
                                        FILTER(?s in (<$uri>) && (LANG(?o)='en' || isNumeric(?o) || isURI(?o) ) ) } ";
    }
    private static function printTriplesLinkedToURI($triple) {
        echo "&lt;" . $triple->s->value . "&gt; &lt" . $triple->p->value . "&gt; &lt;" . $triple->o->value . "&gt; <br/>";
    }
    
    private static function requestTripleFromPredicate($uri, $p) {
        return "SELECT ?s ?p ?o WHERE { ?s $p ?o. FILTER(?s in (<$uri>)) } ";
    }
    private static function formatTripleFromPredicate($triple, $p) {
        return "&lt;" . $triple->s->value . "&gt; &lt" . $p . "&gt; &lt;" . $triple->o->value . "&gt; <br/>";
    }
    
    private static function getRecipesLinkedToURI($uri) {
        return "SELECT ?recipe WHERE {   ?recipe dbo:ingredient dbr:". end(explode('/', $uri)) ." } ";
    }
    
    // Do not use it separately - used in getTriplesFromPredicate
    private static function constructTriplesFromPredicate($response, $predicate) {
        // convert json response to php object
        $spo_triples = json_decode($response);
        $triples = (array)$spo_triples->results->bindings;
        
        // format triples to a clean array
        $formattedTriples = array(); 
        foreach($triples as $triple) {
            $formattedTriples = array_merge(
                $formattedTriples,
                array( array($triple->s->value, $predicate, $triple->o->value) )
            );
        }
        
        return $formattedTriples;
    }
    
    // Returns an array of all triples found for a predicate on a uri
    private static function getTripleFromPredicate($uri, $predicate) {
        $query = self::buildHTTPRequest(self::requestTripleFromPredicate($uri, $predicate));
        $response = file_get_contents($query);
        $triples = self::constructTriplesFromPredicate($response, $predicate);
        return $triples;
    }
    
    /**
     * @brief Treat function, enhances graph extracting values of the required predicate
     * @param array $results
     *      Array of uris to scan using predicates
     * @param array $requiredPredicates
     *      Array of predicates to use to enhance graph
     */
    public static function Treat($results, $requiredPredicates) {
        // Execution des requetes
        $requests = array();
        $allTriples = array(); 
        
        foreach($results as $key => $uris) {
            foreach($uris as $word => $uri) {
                foreach($requiredPredicates as $predicate) {
                    $triples = self::getTripleFromPredicate($uri, $predicate);
                    if(!empty($triples)) {
                        $allTriples = array_merge($allTriples, $triples);
                    }
                }
            }
        }    
        
        return $allTriples;
    }
    
    /**
     * @brief Test function associated to Treat function
     */ 
    public static function TreatTest(){
        
        //
        $REQUIRED_PREDICATES = array("dbp:fat", "dbp:kj");
    
        // Tableau des URI générées par le module 3
        // Recette : riz petits pois et herbes de provence
        $RESULTS = array(
           "http://www.lemonade.org" =>
                array(     
                   'rice' => 'http://dbpedia.org/resource/Rice',
                   'peas' => 'http://dbpedia.org/resource/Pea',
                   'herbes_de_provence' => 'http://dbpedia.org/resource/Herbes_de_Provence'
                ),
            "http://www.miam.fr/recette/sandwich_poisson" => 
            array(
                'bread' => 'http://dbpedia.org/page/Bread',
                'butter' => 'http://dbpedia.org/page/Butter',
                'tomato' => 'http://dbpedia.org/page/Tomato',
                'emmental' => 'http://dbpedia.org/page/Emmental_cheese',
                'lettuce' => 'http://dbpedia.org/page/Lettuce'
            )
        );

        return self::Treat($RESULTS, $REQUIRED_PREDICATES);
    }
}