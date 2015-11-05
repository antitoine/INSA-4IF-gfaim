<?php

/**
 * Static class with methods to start a request, to search data related to recipies.
 */
class GFaimSearchEngine {

    /**
     * Returns data associated to the concept related to the uri passed by
     * parameter.
     * @param uri The uri of the concept to search
     * @return Associative array
     *  - name : The name of the concept
     *  - description : The description of the concept
     *  - wikipediaUrl : The url of the wikipedia link
     *  - image : The url of the concept's image
     *  - imageCaption : The caption of the concept's image
     *  - uri : The uri of the concept
     *  - recipies : Associative array with the main recipies
     *      - name : The name of the recipie
     *      - wikipediaUrl : The url of the wikipedia link
     *      - image : The url of the recipie's image
     */
    private static function getConceptData($uri) {

        $generalInfo = ResultEnhancer::getGeneralInfos($uri);

        $conceptData = array(
            'name' => '',
            'description' => '',
            'wikipediaUrl' => '',
            'image' => '',
            'imageCaption' => '',
            'uri' => $uri,
            'recipies' => array()
        );
        
        $recipies = array();

        foreach ($generalInfo as $triple) {
            switch ($triple[1]) {
                case GENERAL_INFO_LABEL:
                    if ($triple[0] == $uri) { // If it's the label of the main concept
                        $conceptData['name'] = $triple[2];
                    } else { // Label of a recipie
                        if (!isset($recipies[$triple[0]])) {
                            if (count($recipies) < NB_MAX_RECIPIES) {
                                $recipies[$triple[0]]['name'] = $triple[2];
                            }
                        } else {
                            $recipies[$triple[0]]['name'] = $triple[2];
                        }
                    }
                    
                    break;
                        
                case GENERAL_INFO_COMMENT:
                    $conceptData['description'] = $triple[2];
                    break;
                    
                case GENERAL_INFO_PRIMARYTOPIC:
                    if ($triple[0] == $uri) { // If it's the url of the main concept
                        $conceptData['wikipediaUrl'] = $triple[2];
                    } else { // Label of a recipie
                        if (!isset($recipies[$triple[0]])) {
                            if (count($recipies) < NB_MAX_RECIPIES) {
                                $recipies[$triple[0]]['wikipediaUrl'] = $triple[2];
                            }
                        } else {
                            $recipies[$triple[0]]['wikipediaUrl'] = $triple[2];
                        }
                    }
                    
                    break;
                    
                case GENERAL_INFO_THUMBNAIL:
                    $conceptData['image'] = $triple[2];
                    break;
                    
                case GENERAL_INFO_IMAGECAPTION:
                    $conceptData['imageCaption'] = $triple[2];
                    break;
                    
                case PROPERTY_RECIPIE_IMAGE:
                    
                    if (!isset($recipies[$triple[0]])) {
                        if (count($recipies) < NB_MAX_RECIPIES) {
                            $recipies[$triple[0]]['image'] = $triple[2];
                        }
                    } else {
                        $recipies[$triple[0]]['image'] = $triple[2];
                    }
                    
                    break;
            }
        }
        
        $conceptData['recipies'] = $recipies;

        return $conceptData;
    }

    /**
     * Add the data related to the main concepts stored in finalData. Update
     * finalData by reference.
     * @param finalData Array with the data to return after the search query.
     * @param rdfData The rdf triples.
     */
    private static function addMainConceptsData(&$finalData, &$rdfData) {
        foreach ($finalData as $key => $connectedComponent) {
            if (isset($connectedComponent['mainConcept']['uri'])) {
                $mainConceptURI = $connectedComponent['mainConcept']['uri'];

                if (!empty($mainConceptURI)) {
                    $finalData[$key]['mainConcept'] = self::getConceptData($mainConceptURI);
                }
            }
        }
    }

    /**
     * Add the extern links get by google search engine, to the final data array.
     * Update finalData by reference.
     * @param finalData Array with the data to return after the search query.
     * @param googleResults The google results.
     */
    private static function addExternLinksData(&$finalData, &$googleResults) {
        foreach ($finalData as $key => $connectedComponent) {
            $externLinks = $connectedComponent['externLinks'];

            if (!empty($externLinks)) {
                foreach ($externLinks as $urlKey => $urlData) {
                    $urlDetails = $googleResults[$urlData['url']];
                
                    $finalData[$key]['externLinks'][$urlKey] = array(
                        'url' => $urlData['url'],
                        'title' => $urlDetails['title'],
                        'description' => $urlDetails['description']
                    );
                }
            }
        }
    }

    /**
     * Give the result of recepies of the GFaimSearchEngine by a query of ingredients
     * @param $query the query of ingredients
     * @param $confidence the level of confidence when spotlight is called (between 0 and 1)
     * @param $similarity the level of similarity when the graph is genereted
     * @return Array of results
     *  - List of associative arrays, for each connected components of results, with :
     *      - graph : The similarity graph
     *      - mainConcept : See getConceptData method for the format
     *      - tags : List of tags
     *      - externLinks : List of extern links with 
     *          - url
     *          - title
     *          - description
     */
    public static function search($query, $confidence = SPOTLIGHT_DEFAULT_CONFIDENCE, $similarity = GRAPH_DEFAULT_SIMILARITY) {

        $googleResults = SearchEngineExtraction::getResultLinksOfQuery($query);

        $annotatedUrls = TextAnnotation::annotateTexts(
                            TextExtractor::getAllText(
                                array_keys($googleResults)
                            ), 
                            $confidence
                        );

        $enhancedResults = ResultEnhancer::Process($annotatedUrls);
        
        $connectedComponents = GraphSimilarity::getConnectedComponentsJSON($enhancedResults, $similarity);

        // Add additional data : complete data for each main concept
        self::addMainConceptsData($connectedComponents, $enhancedResults);

        // Add additional data : complete data for each extern links
        self::addExternLinksData($connectedComponents, $googleResults);

        return $connectedComponents;
    }
}