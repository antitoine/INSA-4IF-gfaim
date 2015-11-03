<?php

class GFaimSearchEngine {
    
    public static function search($query, $thresholdSimilarity = 1.2) {
        
        $annotatedUrls = TextAnnotation::annotateTexts(
                            TextExtractor::getAllText(
                                SearchEngineExtraction::getResultLinksOfQuery(
                                    $query
                                )
                            )
                        );
                    
        $enhancedResults = ResultEnhancer::Process($annotatedUrls);
    
        $connectedComponents = GraphSimilarity::getConnectedComponentsJSON($enhancedResults, $thresholdSimilarity);
    
    echo '<pre>';
    var_dump($connectedComponents);
    echo '</pre>';

        return $connectedComponents;
    }
}