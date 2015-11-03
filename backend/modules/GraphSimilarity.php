<?php

/**
 * Static class with methods to compare the similarities between rdg graphs, in
 * order to group the results by main concept groups.
 */
class GraphSimilarity
{
    /**
     * Calculate the similarity index between two rdf data lists, using the
     * jaccard similarity index.
     * @param rdfDataList1 The array with the rdf data of the first graph to
     * compare. The array is a list of sub-array with 3 values, representing the
     * rdf triples)
     * @param rdfDataList2 The array with the rdf data of the second graph to
     * compare. The array is a list of sub-array with 3 values, representing the
     * rdf triples).
     * @return The similarity index.
     */
    private static function getSimilarityIndex(array $rdfDataList1, array $rdfDataList2)
    {
         
        // If both rdf data are empty, the jaccard index is equals to 1.
        if (empty($rdfDataList1) && empty($rdfDataList2))
        {
            return 1;
        }
        
        // Create the nodes of the rdf data list 1
        $nodesList1 = array();
        
        foreach ($rdfDataList1 as $rdfData)
        {
            $nodesList1[] = $rdfData[0] . '-' . $rdfData[1] . '-' . $rdfData[2];
        }
        
        // Create the nodes of the rdf data list 2
        $nodesList2 = array();
    
        foreach ($rdfDataList2 as $rdfData) 
        {
            $nodesList2[] = $rdfData[0] . '-' . $rdfData[1] . '-' . $rdfData[2];
        }

        $nodesIntersect = array_intersect($nodesList1, $nodesList2);
        
        $nodesUnion = array_unique(array_merge($nodesList1, $nodesList2));

        // Compute the jaccard index
        $jaccard = count($nodesIntersect) / count($nodesUnion); 
        
        return $jaccard;
    }
    
    
    public static function computeGlobalGraphResults(array $rdfGraphs, $similarityThreshold)
    {
        $globalGraph = new Graph();
        
        foreach ($rdfGraphs as $urlGraph1 => $rdfGraph)
        {
            foreach ($rdfGraphs as $urlGraph2 => $rdfGraph2)
            {
                if ($urlGraph1 != $urlGraph2)
                {
                    $similarity = self::getSimilarityIndex($rdfGraph, $rdfGraph2);
                    
                    //echo $urlGraph1 . ' vs ' . $urlGraph2 . '<br>';
                    //echo $similarity . ' / ' . $similarityThreshold . '<br>';

                    // Add the arc to ne global graph results
                    if ($similarity >= $similarityThreshold)
                    {
                        $globalGraph->addEdge($urlGraph1, $urlGraph2, $similarity);
                    }
                    else 
                    {
                        $globalGraph->addNode($urlGraph1);
                        $globalGraph->addNode($urlGraph2);
                    }
                }
            }
            
        }
        
        $connectedComponentsGraph = $globalGraph->getConnectedComponentsAsGraphs();
        
        return $connectedComponentsGraph;
    }
    
    public static function getConnectedComponentsJSON(array $rdfResults, $similarityThreshold)
    {
        // Compute and get the connected components graph 
        $connectedComponentsGraph = self::computeGlobalGraphResults($rdfResults, $similarityThreshold);
        
        // Create the array to return
        $arrayJSON = array();
        
        foreach ($connectedComponentsGraph as $graph) 
        {
            $arrayJSON[] = $graph->asArray(); 
        }
        
        return $arrayJSON;
    }
    
    public static function getConnectedComponentsJSONTest()
    {
        $rdfGraphs = array(
            'url 1' => array(
                array('http://dbpedia.org/resource/Pea', 'dbp:f5at', '1.0'),
                array('http://dbpedia.org/resource/Pea', 'dbp:fat', '0.4'),
                array('http://dbpedia.org/resource/Pea', 'dbp:kj', '339'),
                array('http://dbpedia.org/resource/Pea', 'dbp:kj', '1425')
            ),
            
            'url 2' => array(
                array('http://dbpedia.org/resource/Pea', 'dbp:fat', '1.0'),
                array('http://dbpedia.org/resource/Pea', 'dbp:fat', '0.4'),
                array('http://dbpedia.org/resource/Pea', 'dbp:kj', '10'),
                array('http://dbpedia.org/resource/Pea', 'dbp:kj', '1425')
            ),
            
            'url 3' => array(
                array('http://dbpedia.org/resource/Pea', 'dif', '1.0'),
                array('http://dbpedia.org/resource/Pea', 'dif', '0.4'),
                array('http://dbpedia.org/resource/Pea', 'dif', '10'),
                array('http://dbpedia.org/resource/Pea', 'dif', '1425')
            )
        );
        
        $rdfGraphsTest = ResultEnhancer::ProcessTest();
        
        return GraphSimilarity::getConnectedComponentsJSON($rdfGraphsTest, 0.1);
    }
}

?>