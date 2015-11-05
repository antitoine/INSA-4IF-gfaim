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
        
        if ($resultOfQueryCached != null && !empty($resultOfQueryCached))
        {
            return $resultOfQueryCached;
        }

        $links = array();

        $googleResultsRecipes = Utils::CallAPI('GET', SEARCH_ENGINE_URL, array(
            'key' => SEARCH_ENGINE_KEYS_RECIPES[$APIKeyNumber],
            'cx' => GOOGLE_SEARCH_CX,
            'q' => 'recipes '.$query,
            'safe' => 'high',
            'lr' => 'lang_en'
        ));

        $googleResultsJSONRecipes = json_decode($googleResultsRecipes, true);

        $googleResultsFood = Utils::CallAPI('GET', SEARCH_ENGINE_URL, array(
            'key' => SEARCH_ENGINE_KEYS_FOOD[$APIKeyNumber],
            'cx' => GOOGLE_SEARCH_CX,
            'q' => 'food '.$query,
            'safe' => 'high',
            'lr' => 'lang_en'
        ));

        $googleResultsJSONFood = json_decode($googleResultsFood, true);
        
        if (!isset($googleResultsJSONRecipes['items']))
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

        foreach ($googleResultsJSONRecipes['items'] as $item)
        {
            $links[$item['link']] = array(
                'title' => $item['title'],
                'description' => $item['snippet']
            ); 
        }
        
        foreach ($googleResultsJSONFood['items'] as $item)
        {
            $links[$item['link']] = array(
                'title' => $item['title'],
                'description' => $item['snippet']
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