<?php

class GFaimSearchEngine {

    private static function getConceptData($uri) {

        $generalInfo = ResultEnhancer::getGeneralInfos($uri);

        $conceptData = array(
            'name' => '',
            'description' => '',
            'wikipediaUrl' => '',
            'image' => '',
            'imageCaption' => '',
            'uri' => $uri
        );

        foreach ($generalInfo as $triple) {
            switch ($triple[1]) {
                case GENERAL_INFO_LABEL:
                    $conceptData['name'] = $triple[2];
                    break;
                        
                case GENERAL_INFO_COMMENT:
                    $conceptData['description'] = $triple[2];
                    break;
                    
                case GENERAL_INFO_PRIMARYTOPIC:
                    $conceptData['wikipediaUrl'] = $triple[2];
                    break;
                    
                case GENERAL_INFO_THUMBNAIL:
                    $conceptData['image'] = $triple[2];
                    break;
                    
                case GENERAL_INFO_IMAGECAPTION:
                    $conceptData['imageCaption'] = $triple[2];
                    break;
            }
        }

        return $conceptData;
    }

    private static function addMainConceptsData(&$finalData, &$rdfData) {
        foreach ($finalData as $key => $connectedComponent) {
            $mainConceptURI = $connectedComponent['mainConcept']['uri'];

            if (!empty($mainConceptURI)) {
                $finalData[$key]['mainConcept'] = self::getConceptData($mainConceptURI);
            }
        }
    }

    public static function addExternLinksData(&$finalData, &$googleResults) {
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
     * @param $confidence the level of confidence when spotlight is call (between 0 and 1)
     * @param $similarity the level of similarity when the graph is genereted
     * @return an array TODO
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