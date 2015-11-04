<?php

/**
 * Class with methods to compare the similarities between rdg graphs, in
 * order to group the results by main concept groups.
 */
class GraphSimilarity
{
    private $graphConcepts;
    
    private function __construct() {
        $this->graphConcepts = array();
    }
    
    private function addConceptToUrl($url, $concept) {
        if (isset($this->graphConcepts[$url][$concept])) {
            $this->graphConcepts[$url][$concept]++;
        } else {
            $this->graphConcepts[$url][$concept] = 0;
        }
    }
    
    private function getMainConceptOfConnectedComponent($connectedComponentGraph) {
        $urls = $connectedComponentGraph->getListOfNodes();
        
        // Sum the concepts occurences
        $conceptsOccurs = array();
        
        foreach ($urls as $url) {
            foreach ($this->graphConcepts[$url] as $concept => $occurs) {
                if (!isset($conceptsOccurs[$concept])) {
                    $conceptsOccurs[$concept] = $occurs;
                } else {
                    $conceptsOccurs[$concept] += $occurs;
                }
            }
        }
        
        if (empty($conceptsOccurs)) {
            return '';
        }
        
        // Get the max value of concept counter
        $maxCounter = max(array_values($conceptsOccurs));
        
        // Get the matching concepts
        $mainConcept = array_search($maxCounter, $conceptsOccurs);
        
        $this->removeConcept($mainConcept);
        
        return $mainConcept;
    }
    
    private function removeConcept($concept) {
        foreach ($this->graphConcepts as $url => $conceptsArray) {
            unset($this->graphConcepts[$url][$concept]);
        }
    }
    
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
    private function getSimilarityIndex($url1, array $rdfDataList1, $url2, array $rdfDataList2)
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
            $this->addConceptToUrl($url1, $rdfData[0]);
        }
        
        // Create the nodes of the rdf data list 2
        $nodesList2 = array();
    
        foreach ($rdfDataList2 as $rdfData) 
        {
            $nodesList2[] = $rdfData[0] . '-' . $rdfData[1] . '-' . $rdfData[2];
            $this->addConceptToUrl($url2, $rdfData[0]);
        }

        $nodesIntersect = array_intersect($nodesList1, $nodesList2);
        
        $nodesUnion = array_unique(array_merge($nodesList1, $nodesList2));

        // Compute the jaccard index
        return count($nodesIntersect) / count($nodesUnion);
    }
    
    private function computeGlobalGraphResults(array $rdfGraphs, $similarityThreshold)
    {
        $globalGraph = new Graph();
        
        $graphAlreadyComputed = array();
        
        foreach ($rdfGraphs as $urlGraph1 => $rdfGraph)
        {
            foreach ($rdfGraphs as $urlGraph2 => $rdfGraph2)
            {
                if ($urlGraph1 != $urlGraph2)
                {
                    // If the similarity is not already checked
                    if (!isset($graphAlreadyComputed[$urlGraph1.$urlGraph2])
                     && !isset($graphAlreadyComputed[$urlGraph2.$urlGraph1])) {
                         
                        $similarity = $this->getSimilarityIndex($urlGraph1, $rdfGraph, $urlGraph2, $rdfGraph2);
                    
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
                        
                        $graphAlreadyComputed[$urlGraph1.$urlGraph2] = true;
                    }
                }
            }
        }
        
        $connectedComponentsGraph = $globalGraph->getConnectedComponentsAsGraphs();
        
        return $connectedComponentsGraph;
    }
    
    private static function compareConnectedComponentsGraphs($connectedComponent1, $connectedComponent2) {
        return $connectedComponent2->nodeCount() - $connectedComponent1->nodeCount();
    }
    
    public static function getConnectedComponentsJSON(array $rdfResults, $similarityThreshold)
    {
        $graphSimilarity = new GraphSimilarity();
        
        // Compute and get the connected components graph 
        $connectedComponentsGraph = $graphSimilarity->computeGlobalGraphResults($rdfResults, $similarityThreshold);
        
        // Sort the connected components graphs by graph size
        uasort($connectedComponentsGraph, 'self::compareConnectedComponentsGraphs');
        
        // Create the array to return
        $connectedComponentsList = array();
        
        foreach ($connectedComponentsGraph as $graph) 
        {
            $connectedComponent = array();
            
            // Set the graph
            $connectedComponent['graph'] = $graph->asArray();
            
            // Detect and set the main concept of the connected component
            $mainConcept = $graphSimilarity->getMainConceptOfConnectedComponent($graph);
            $connectedComponent['mainConcept'] = array(
                'uri' => $mainConcept
            );
            
            // Set the external links
            $externLinks = array();
            foreach ($graph->getListOfNodes() as $url) {
                $externLinks[] = array(
                    'url' => $url
                );
            }
            $connectedComponent['externLinks'] = $externLinks;
            
            $connectedComponentsList[] = $connectedComponent;
        }
        
        return $connectedComponentsList;
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
        
        //echo 'ResultEnhancer::ProcessTest___________________<br>';
        
        //$rdfGraphsTest = ResultEnhancer::ProcessTest();
        
        return GraphSimilarity::getConnectedComponentsJSON($rdfGraphs, 0.1);
    }
}

?>