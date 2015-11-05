<?php

class ResultEnhancer {
    
    // --------------------------------- CONFIG --------------------------------
    
    const REQUEST_OUTPUT_FORMAT = "json"; // Can be xml, json, text-turtle ... 
    const TIMEOUT = 30000;
    const GRAPH_URI = "http://dbpedia.org";
    const RESULT_ONLY = TRUE;
    
    //SPARQL config
    const RSPARQL_PATH = '/opt/apache-jena-3.0.0/bin/';
    const SPARQL_QUERY_FILEPATH = '/var/www/gfaim/backend/var/result_enhancer_query/query.sparql'; 
    const SPARQL_QUERY_RESULT_FILEPATH = '/var/www/gfaim/backend/var/result_enhancer_query/queryResults.json';
    const SPARQL_ENDPOINT = 'http://dbpedia.org/sparql';

    // 
    // ASK et SELECT formats : 
    //      json,text,xml,csv,tsv
    // 
    // CONSTRUCT et DESCRIBES formats :
    //      turtle,rdf,n-tuples
    //
    const SPARQL_RESULT_FORMAT = 'json'; 
    
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
        return "SELECT ?s ?p ?o WHERE { ?s $p ?o. FILTER(?s in (<$uri>) && (LANG(?o)='en' || LANG(?o)='' )) } ";
    }
    
    private static function requestAllTriples($uri, $englishPredicates, $predicatesNoLang) {
        $predicatesFilter = self::getPredicatesListFilter($uri, $englishPredicates, $predicatesNoLang);
        
        return "SELECT ?s ?p ?o WHERE { " . $predicatesFilter . " } ";
    }
    
    private static function formatTripleFromPredicate($triple, $p) {
        return "&lt;" . $triple->s->value . "&gt; &lt;" . $p . "&gt; &lt;" . $triple->o->value . "&gt; <br/>";
    }
    
    private static function getRecipesLinkedToURI($uri) {
        return "SELECT ?recipe WHERE {   ?recipe dbo:ingredient dbr:". end(explode('/', $uri)) ." } ";
    }
    
    private static function getPredicatesListFilter($uri, $englishPredicates, $predicatesNoLang) {
        
        $filters = array();
        
        foreach ($englishPredicates as $predicate) {
            $filters[] = '{ ?s ?p ?o. FILTER(?s in (<'. $uri .'>) '. 
                                            ' && (LANG(?o)=\'en\' || LANG(?o)=\'\') '.
                                            ' && (?p in (<'.$predicate.'>)) '.
                                            ') }';
        }
        
        foreach ($predicatesNoLang as $predicate) {
            
            if ($predicate == PROPERTY_GENUS) {
                $filters[] = '{ <'.$uri.'> <'.PROPERTY_GENUS.'> ?genus. '.
                             '?s ?p ?o. '.
                             'FILTER (?p in (<'.PROPERTY_GENUS.'>) && ?o in (?genus)) }';
            } else if ($predicate == PROPERTY_MAININGREDIENTOF) {
                $filters[] = '{ ?recipie <'.PROPERTY_MAININGREDIENTOF.'> <'.$uri.'>. '.
                                '{ ?s ?p ?o. FILTER (?s in (?recipie) && ?p in (<'.GENERAL_INFO_LABEL.'>) && (LANG(?o)=\'en\' || LANG(?o)=\'\')) } '.
                          'UNION { ?s ?p ?o. FILTER (?s in (?recipie) && ?p in (<'.PROPERTY_RECIPIE_IMAGE.'>)) } '.
                          'UNION { ?s ?p ?o. FILTER (?s in (?recipie) && ?p in (<'.GENERAL_INFO_PRIMARYTOPIC.'>)) } } ';
            } else {
                $filters[] = '{ ?s ?p ?o. FILTER(?s in (<'. $uri .'>) '. 
                                                ' && (?p in (<'.$predicate.'>)) '.
                                                ') }';
            }
        }
        
        return implode(' UNION ', $filters);
    }
    
    
    public static function getGeneralInfos($uri) {
        $predicatesEnglish = array(GENERAL_INFO_LABEL, GENERAL_INFO_COMMENT, GENERAL_INFO_IMAGECAPTION);
        $predicates = array(GENERAL_INFO_THUMBNAIL, GENERAL_INFO_PRIMARYTOPIC, PROPERTY_MAININGREDIENTOF);

        return self::getAllTriples($uri, $predicatesEnglish, $predicates);
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
    
    // Do not use it separately - used in getTriplesFromPredicate
    private static function constructAllTriples($response) {
        
        // convert json response to php object
        $spo_triples = json_decode($response, true);
        
        
        $triples = $spo_triples['results']['bindings'];
        
        if (empty($triples)) {
            return array();
        }
        
        
        // format triples to a clean array
        $formattedTriples = array();
        foreach (array_values($triples) as $triple) {
            
                $formattedTriples = array_merge(
                    $formattedTriples,
                    array( array($triple['s']['value'], $triple['p']['value'], $triple['o']['value']) )
                );   
            
        }
        return $formattedTriples;
    }
    
    private static function getAllTriples($uri, $englishPredicates, $predicatesNoLang) {
        // Remote version - fast
        $query = self::buildHTTPRequest(self::requestAllTriples($uri, $englishPredicates, $predicatesNoLang));
        $response = file_get_contents($query);
        return self::constructAllTriples($response);
    }
    
    private static function getAllTriplesGenus($uriGenus) {

        $query = self::buildHTTPRequest(self::requestAllTriplesGenus($uriGenus));
        $response = file_get_contents($query);

        $triples = self::constructAllTriples($response);
        return $triples;
    }
    
    private static function isURL($url) {
        return (!filter_var($url, FILTER_VALIDATE_URL) === false);
    }
    
    /**
     * @brief Process function, enhances graph extracting values of the required predicate
     * @param array $results
     *      Array of uris to scan using predicates
     * @param array $requiredPredicates
     *      Array of predicates to use to enhance graph
     */
    public static function Process($results, $englishPredicates = array("http://www.w3.org/2000/01/rdf-schema#label",
                                                                 "http://dbpedia.org/property/fat", 
                                                                 "http://dbpedia.org/property/protein",
                                                                 "http://dbpedia.org/property/calciumMg",
                                                                 "http://dbpedia.org/property/kj",
                                                                 ), $predicatesNoLang = array(PROPERTY_GENUS)) {
                                                                            /* 
                                                                        "rdfs:comment",
                                                                        "foaf:isPrimaryTopicOf",
                                                                        "dbo:thumbnail", 
                                                                        "dbp:imageCaption", */
        // Execution des requetes
        $requests = array();
        
        $uriTriples = array();
        
        $resultProcess = array();
        // Foreach list of uris
        foreach($results as $url => $uris) {
            $allTriples = array(); 
            // Foreach uri in the array  
            foreach($uris as $word => $uri) {
                if (!isset($uriTriples[$uri])) {
                    $triples = self::getAllTriples($uri, $englishPredicates, $predicatesNoLang);
                    
                    if(!empty($triples)) {
                        $uriTriples[$uri] = $triples;
                        $allTriples = array_merge($allTriples, $triples);
                    }
                } else {
                    $allTriples = array_merge($allTriples, $uriTriples[$uri]);
                }
            }
            $resultProcess[$url] = $allTriples;
        }
        return $resultProcess;
    }
    
    /**
     * @brief Test function associated to Process function
     */ 
    public static function ProcessTest(){
        // Tableau des URI générées par le module 3
        // Recette : riz petits pois et herbes de provence
        $RESULTS = array(
           "http://www.lemonade.org" =>
                array(     
                   'rice' => 'http://dbpedia.org/resource/Pea',
                   'peas' => 'http://dbpedia.org/resource/Pea',
                   'herbes_de_provence' => 'http://dbpedia.org/resource/Herbes_de_Provence'
                ),
            "http://www.miam.fr/recette/sandwich_poisson" => 
            array(
                'bread' => 'http://dbpedia.org/resource/Bread',
                'butter' => 'http://dbpedia.org/resource/Butter',
                'tomato' => 'http://dbpedia.org/resource/Tomato',
                'emmental' => 'http://dbpedia.org/resource/Emmental_cheese',
                'lettuce' => 'http://dbpedia.org/resource/Lettuce'
            )
        );

        return self::Process($RESULTS);
    }
    
    public static function ProcessTestDataConcept() {
        $uri = 'http://dbpedia.org/resource/Tomato';
        
        return self::getGeneralInfos($uri);
    }
}