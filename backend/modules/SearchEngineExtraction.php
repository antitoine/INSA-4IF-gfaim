<?php

/**
 * Static class with methods to execute queries through a google custom search
 * engine.
 */
class SearchEngineExtraction
{

    /**
     * Execute a query through the google custom search engine.
     * @param query The query to execute.
     * @return Collection of links returned by the google custom search engine.
     */
    public static function getResultLinksOfQuery($query)
    {

        $cache = Cache::getInstance();
        $resultOfQueryCached = $cache->getResultsSearchByQuery($query);
        
        if (!empty($resultOfQueryCached)) {
            return $resultOfQueryCached;
        }

        $links = array();

        switch (SEACH_ENGINE) {
            case 'google':

                $googleResults = \Utils\Utils::CallAPI('GET', SEARCH_ENGINE_URL, array(
                    'key' => SEARCH_ENGINE_KEY3,
                    'cx' => GOOGLE_SEARCH_CX,
                    'q' => $query,
                    'safe' => 'high',
                    'lr' => 'lang_en'
                )); 
                
                $googleResultsJSON = json_decode($googleResults);

                if (!isset($googleResultsJSON->{'items'})) {
                    return $links;
                }

                foreach ($googleResultsJSON->{'items'} as $item)
                {
                    $links[] = $item->{'link'};
                }
                break;
            // TODO case 'duckduckgo'
            // TODO case 'yahoo'
        }

        $cache->setResultsSearchQuery($query, $links);

        return $links;
    }

    /**
     * Execute a query ('pineapple juice') through the google custom search engine.
     * @return Collection of links returned by the google custom search engine.
     */
    public static function getResultOfTestQuery() {
        return SearchEngineExtraction::getResultLinksOfQuery('tomato chocolate');
    }

}