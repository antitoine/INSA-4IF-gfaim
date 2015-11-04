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
    public static function getResultLinksOfQuery($query, $APIKeyNumber = 0)
    {
        $cache = Cache::getInstance();
        $resultOfQueryCached = $cache->getResultListOfModuleOneByQuery($query);
        
        if (!empty($resultOfQueryCached))
        {
            return $resultOfQueryCached;
        }

        $links = array();

        $googleResults = Utils::CallAPI('GET', SEARCH_ENGINE_URL, array(
            'key' => SEARCH_ENGINE_KEYS[$APIKeyNumber],
            'cx' => GOOGLE_SEARCH_CX,
            'q' => $query,
            'safe' => 'high',
            'lr' => 'lang_en'
        )); 

        $googleResultsJSON = json_decode($googleResults);

        if (!isset($googleResultsJSON->{'items'}))
        {
            if ($APIKeyNumber < NUMBER_OF_SEARCH_ENGINE_KEYS)
            {
                return self::getResultLinksOfQuery($query, $APIKeyNumber + 1);
            }
            else
            {
                return $links;   
            }
        }

        foreach ($googleResultsJSON->{'items'} as $item)
        {
            $links[$item->{'link'}] = array(
                'title' => $item->{'title'},
                'description' => $item->{'title'}
            ); 
        }

        $cache->setResultListInModuleOne($query, $links);

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