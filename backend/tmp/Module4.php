<?php

class enrichisseurDeResultat {
    
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
    
    public static function Treat($results, $requiredPredicates) {
        // Execution des requetes
        $requests = array();
        foreach($results as $key => $uris) {
            $allTriples = array(); 
            foreach($uris as $word => $uri) {
                foreach($requiredPredicates as $predicate) {
                    $triples = self::getTripleFromPredicate($uri, $predicate);
                    if(!empty($triples)) {
                        $allTriples = array_merge($allTriples, $triples);
                    }
                }
            }
            echo "<pre>";
            print_r($allTriples);
            echo "</pre>";
        }     
    }
    
    // if($DEBUG) {
    //     // HTML degueu pour essayer
    //     echo "<!DOCTYPE html>";
    //     echo "<html>";
    //     echo "  <head>";
    //     echo "<!-- Latest compiled and minified CSS -->
    //     <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css\">
    //     <!-- Optional theme -->
    //     <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css\">
    //     <!-- Latest compiled and minified JavaScript -->
    //     <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js\"></script>";
    //     echo "  </head>";
    //     echo "  <body>";
    //     echo "      <div class=\"container-fluid\">";
    //     echo "          <div class=\"row\">";
    //     echo "            <div class=\"col-md-12\">";
    //     echo "                  <table class=\"table\">";
    //     foreach($requests as $req => $resp) {
    //         if(!$RESULT_ONLY) {
    //         echo "<tr><td> Executing query : " . htmlspecialchars(urldecode($req)) . "</td></tr>";
    //         }
    //         echo "<tr><td><pre>";
    //       // $triples = (array)$resp->results->bindings;
    //         //foreach($triples as $triple) {
    //           // printNutritionalInfos($triple, "dbp:fat");
    //         //}
    //         echo "</pre></td></tr>";
    //     }
    //     echo "                  </table>";
    //     echo "              </div>";
    //     echo "          </div>";
    //     echo "      </div>";
    //     echo "  </body>";
    //     echo "</html>";
    // }
}

$REQUIRED_PREDICATES = array("dbp:fat", "dbp:kj");

// Tableau des URI générées par le module 3
    // Recette : riz petits pois et herbes de provence
    $RESULTS = array(
       "http://www.lemonade.org" =>
    array(
                       
                       'rice' => 'http://dbpedia.org/resource/Rice',
                       'peas' => 'http://dbpedia.org/resource/Pea',
                       'herbes_de_provence' => 'http://dbpedia.org/resource/Herbes_de_Provence'
       )
    );
    
enrichisseurDeResultat::Treat($RESULTS, $REQUIRED_PREDICATES);