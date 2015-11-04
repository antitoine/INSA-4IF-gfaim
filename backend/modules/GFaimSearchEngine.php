<?php

class GFaimSearchEngine {
    
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
    }
    
    private static function addMainDataData(&$finalData, &$rdfData) {
        foreach ($finalData as $key => $connectedComponent) {
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
        }
    }
    
    public static function search($query, $thresholdSimilarity = 0.2) {
        
        $annotatedUrls = TextAnnotation::annotateTexts(
                            TextExtractor::getAllText(
                                SearchEngineExtraction::getResultLinksOfQuery(
                                    $query
                                )
                            )
                        );
                    
        $enhancedResults = ResultEnhancer::Process($annotatedUrls);
    
   //     $connectedComponents = GraphSimilarity::getConnectedComponentsJSON($enhancedResults, $thresholdSimilarity);
    
    //echo '<pre>';
    //var_dump($connectedComponents);
    //echo '</pre>';
    
        // Add additional data : complete data for each main concept
       /// self::addMainDataData($connectedComponents, $enhancedResults);
        
        
       // echo '--------------------------------------------------------------------------<br><pre>';
    //var_dump($connectedComponents);
    //echo '</pre>';*/

        return $enhancedResults;
    }
}