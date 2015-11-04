<?php

class GFaimSearchEngine {
    /*
    private static function getConceptData(&$rdfData, $conceptUri) {
        $label = '';
        $image = '';
        $imageCaption = '';
        $comment = '';
        
        foreach ($rdfData as $triplesList) {
            foreach ($triplesList as $triple) {
                if ($triple[0] == $conceptUri) {
                    switch ($triple[1]) {
                        case 'rdfs:label':
                            $label = $triple[2];
                            break;
                        
                        case 'rdfs:comment':
                            $comment = $triple[2];
                            break;
                            
                        case 'dbo:thumbnail':
                            $image = $triple[2];
                            break;
                        
                        case 'dbp:imageCaption':
                            $imageCaption = $triple[2];
                            break;
                    }
                    
                    if (!empty($label) && !empty($image) && !empty($imageCaption) && !empty($comment)) {
                        break 2;
                    }
                }
            }
        }
        
        return array(
            'label' => $label,
            'comment' => $comment,
            'image' => $image,
            'imageCaption' => $imageCaption
        );
    }*/
    
    private static function addMainDataData(&$finalData, &$rdfData) {
        /*foreach ($finalData as $key => $connectedComponent) {
            $mainConceptURI = $connectedComponent['mainConcept']['uri'];
            
            if (!empty($mainConceptURI)) {
                $dataMainConception = self::getConceptData($mainConceptURI);
                $finalData[$key]['mainConcept'] = array(
                    'name' => $dataMainConception['label'],
                    'uri' => $mainConceptURI,
                    'image' => $dataMainConception['image'],
                    'imageCaption' => $dataMainConception['imageCaption'],
                    'description' => $dataMainConception['comment']
                );
            }
        }*/
        
        foreach ($finalData as $key => $connectedComponent) {
            $mainConceptURI = $connectedComponent['mainConcept']['uri'];
            
            if (!empty($mainConceptURI)) {
                $conceptDetails = ResultEnhancer::getGeneralInfos($mainConceptURI);
                echo '<pre>';
                var_dump($conceptDetails);
                echo '</pre>';
                /*
                $finalData[$key]['mainConcept'] = array(
                    'name' => $conceptDetails[0],
                    'description' => $conceptDetails[1],
                    'wikipediaUrl' => $conceptDetails[2],
                    'image' => $conceptDetails[3],
                    'imageCaption' => $conceptDetails[4],
                    'uri' => $mainConceptURI
                );*/
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
    
    public static function search($query, $thresholdSimilarity = 0.2) {
        
        $googleResults = SearchEngineExtraction::getResultLinksOfQuery($query);
        
        $annotatedUrls = TextAnnotation::annotateTexts(
                            TextExtractor::getAllText(
                                array_keys($googleResults)
                            )
                        );
                    
        $enhancedResults = ResultEnhancer::Process($annotatedUrls);
    
        $connectedComponents = GraphSimilarity::getConnectedComponentsJSON($enhancedResults, $thresholdSimilarity);
    
    //echo '<pre>';
    //var_dump($connectedComponents);
    //echo '</pre>';
    
        // Add additional data : complete data for each main concept
        //self::addMainDataData($connectedComponents, $enhancedResults);
        
        // Add additional data : complete data for each extern links
        self::addExternLinksData($connectedComponents, $googleResults);
        
        
       // echo '--------------------------------------------------------------------------<br><pre>';
    //var_dump($connectedComponents);
    //echo '</pre>';*/

        return $connectedComponents;
    }
}