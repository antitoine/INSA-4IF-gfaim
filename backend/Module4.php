<?php

// --------------------------------- CONFIG --------------------------------
$DEBUG = TRUE;

function buildHTTPRequest($sparqlQuery) {
    $REQUEST_OUTPUT_FORMAT = "text-turtle"; // Can be xml, json, ...
    $TIMEOUT = 30000;
    $GRAPH_URI = "http%3A%2F%2Fdbpedia.org";
    return "http://dbpedia.org/sparql?default-graph-uri=$GRAPH_URI&query=" . urlencode($sparqlQuery) . "&format=$REQUEST_OUTPUT_FORMAT&timeout=$";
}
$RESULT_ONLY=TRUE;
// -------------------------------------------------------------------------

// Tableau des URI générées par le module 3
// Recette : riz petits pois et herbes de provence
$results = array(
   "A refreshing and light alternative to lemonade. A syrup made with pineapple juice and lemon juice is mixed with carbonated water over ice." =>
array(
                   'rice' => 'http://dbpedia.org/resource/Rice',
                   'peas' => 'http://dbpedia.org/resource/Pea',
                   'herbes_de_provence' => 'http://dbpedia.org/resource/Herbes_de_Provence'
   )
);

// Fonction creation de requete 
function getTriplesLinkedToURI($uri) {
    return "CONSTRUCT { ?s ?p ?o } WHERE { ?s ?p ?o. FILTER(?s in (<$uri>) && (LANG(?o)='en' || isNumeric(?o) || isURI(?o) ) ) } ";
}

function getRecipesLinkedToURI($uri) {
    return "CONSTRUCT { ?recipe } WHERE {   ?recipe dbo:ingredient dbr:". end(explode('/', $uri)) ." } ";
}

// Execution des requetes
$requests = array();
foreach($results as $key => $uris) {
    foreach($uris as $word => $uri) {
        $query = buildHTTPRequest(getTriplesLinkedToURI($uri));
        $response = file_get_contents($query);
        echo $response;
        // Creation  du tableau php
        //$spo_triples = json_decode($response);
        //$requests = array_merge(
        //    $requests, 
        //    array($query => $spo_triples)
        //);
    }                                                                                                                                                                                                     
}     

// if($DEBUG) {
// // HTML degueu pour essayer
// echo "<!DOCTYPE html>";
// echo "<html>";
// echo "  <head>";
// echo "<!-- Latest compiled and minified CSS -->
// <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css\">
// <!-- Optional theme -->
// <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css\">
// <!-- Latest compiled and minified JavaScript -->
// <script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js\"></script>";
// echo "  </head>";
// echo "  <body>";
// echo "      <div class=\"container-fluid\">";
// echo "          <div class=\"row\">";
// echo "            <div class=\"col-md-12\">";
// echo "                  <table class=\"table\">";
// foreach($requests as $req => $resp) {
//     if(!$RESULT_ONLY) {
//     echo "<tr><td> Executing query : " . htmlspecialchars(urldecode($req)) . "</td></tr>";
//     }
//     echo "<tr><td><pre>";
//     $triples = (array)$resp->results->bindings;
//     foreach($triples as $triple) {
//     echo "&lt;" . $triple->s->value . "&gt; &lt" . $triple->p->value . "&gt; &lt;" . $triple->o->value . "&gt; <br/>";
//     }
//     echo "</pre></td></tr>";
// }
// echo "                  </table>";
// echo "              </div>";
// echo "          </div>";
// echo "      </div>";
// echo "  </body>";
// echo "</html>";
// }
