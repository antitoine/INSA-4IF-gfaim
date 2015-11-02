<?php

/**
 * Static class with methods to inspect texts and detect the key words and the
 * DBPedia URIs.
 */
class TextAnnotation
{

    /**
     * Analyse the text passed by parameter and annotate it by calling dbpedia spotlight.
     * @param text The text to inspect
     * @return Associative array, key = the key word, value = the associated URI
     */
    public static function annotate($text)
    {
        $apiResults = Utils\Utils::CallAPI(
            'POST', 'http://spotlight.dbpedia.org/rest/annotate', 
            'text=' . urlencode($text) . '&confidence=0&types=' . urlencode('freebase:food') 
        );
    
        $apiResultsJSON = json_decode($apiResults);
        
        // There is no result returned by the spotlight API
        if (empty($apiResultsJSON) || !isset($apiResultsJSON->{'Resources'})) 
        {
            return array();
        }
        
        // Format the results in a new associative array
        $annotateResults = array();
    
        foreach ($apiResultsJSON->{'Resources'} as $resource)
        {
            $annotateResults[$resource->{'@surfaceForm'}] = $resource->{'@URI'};
        }
        
        return $annotateResults;
    }
    
    /**
     * Analyse the collection of texts passed by parameter and annotate them.
     * @param texts The array of texts to inspect
     * @return Associative array :
     *  key = inspected text, 
     *  value = associative array
     *      key = key work spotted
     *      value = associated dbpedia URI
     */
    public static function annotateTexts(array $texts) 
    {
        $annotateResults = array();
        
        foreach ($texts as $text)
        {
            $annotateResults[$text] = self::annotate($text);
        }
        
        return $annotateResults;
    }

    public static function testAnnoteTexts()
    {
        return TextAnnotation::annotateTexts(
            array(
                    'Banana.',
                    'Chicken, pineapple, avocado, and black beans bring all of the flavors of Cuba to romaine lettuce!  I came up with this recipe to use leftover chicken in a way that combines all of the delicious Cuban flavors I grew up with.'    
                ));
    }

}