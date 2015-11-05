<?php

/**
 * Static class with methods to inspect texts and detect the key words and the
 * DBPedia URIs.
 */
class TextAnnotation
{

    /**
     * Analyse the text passed by parameter and annotate it by calling dbpedia spotlight.
     * @param $text The text to inspect
     * @param $confidence The level of confidence (between O and 1)
     * @return Associative array, key = the key word, value = the associated URI
     */
    public static function annotate($text, $confidence = SPOTLIGHT_DEFAULT_CONFIDENCE)
    {
        $apiResults = Utils::CallAPI(
            'POST', SPOTLIGHT_URL,
            'text=' . urlencode($text) . '&confidence='. $confidence .'&types=' . urlencode('freebase:food')
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
     * @param $urlTextList Associative array :
     *     key = source url
     *     value = full text
     * @param $confidence The level of confidence (between O and 1)
     * @return Associative array :
     *     key = inspected text,
     *     value = associative array
     *         key = key work spotted
     *         value = associated dbpedia URI
     */
    public static function annotateTexts(array $urlTextList, $confidence = SPOTLIGHT_DEFAULT_CONFIDENCE)
    {
        $annotateResults = array();

        foreach ($urlTextList as $url => $text)
        {
            $annotateResults[$url] = self::annotate($text, $confidence);
        }
        return $annotateResults;
    }

    /**
     * Analyse a collection of texts.
     * @return Associative array :
     *  key = source url,
     *  value = associative array
     *      key = key work spotted
     *      value = associated dbpedia URI
     */
    public static function annotateTextsTest()
    {
        return self::annotateTexts(
            array(
              'http://www.healthline.com/health/food-nutrition/pineapple-juice-benefits' => 'Chicken, pineapple, avocado, and black beans bring all of the flavors of Cuba to romaine lettuce!  I came up with this recipe to use leftover chicken in a way that combines all of the delicious Cuban flavors I grew up with.'
            ));
    }

}