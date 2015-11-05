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
     * @param nbMaxResults The number max of links to return
     * @return Collection of links returned by the google custom search engine.
     */
    public static function getResultLinksOfQuery($query, $nbMaxResults = DEFAULT_NUMBER_OF_PAGES, $APIKeyNumber = 0)
    {
        // If the nb max of results is equals to the default number, we cas use
        // the cache
        if ($nbMaxResults == DEFAULT_NUMBER_OF_PAGES) {
            $cache = Cache::getInstance();
            $resultOfQueryCached = $cache->getResultListOfModuleOneByQuery($query);
            
            if ($resultOfQueryCached != null && !empty($resultOfQueryCached))
            {
                return $resultOfQueryCached;
            }
        }
        
        $nbMaxResultsRecipes = ($nbMaxResults > 10) ? 10 : $nbMaxResults;
        $nbMaxResultsFood = ($nbMaxResults > 10) ? $nbMaxResults - 10 : 0;
        
        $links = array();

        $googleResultsRecipes = Utils::CallAPI('GET', SEARCH_ENGINE_URL, array(
            'key' => SEARCH_ENGINE_KEYS_RECIPES[$APIKeyNumber],
            'cx' => GOOGLE_SEARCH_CX,
            'q' => 'recipes '.$query,
            'safe' => 'high',
            'lr' => 'lang_en',
            'num' => $nbMaxResultsRecipes
        ));

        $googleResultsJSONRecipes = json_decode($googleResultsRecipes, true);
        
        if ($nbMaxResultsFood > 0) {
            $googleResultsFood = Utils::CallAPI('GET', SEARCH_ENGINE_URL, array(
                'key' => SEARCH_ENGINE_KEYS_FOOD[$APIKeyNumber],
                'cx' => GOOGLE_SEARCH_CX,
                'q' => 'food '.$query,
                'safe' => 'high',
                'lr' => 'lang_en',
                'num' => $nbMaxResultsFood
            ));
    
            $googleResultsJSONFood = json_decode($googleResultsFood, true);
        }
        
        if (!isset($googleResultsJSONRecipes['items']))
        {
            if ($APIKeyNumber < NUMBER_OF_SEARCH_ENGINE_KEYS)
            {
                return self::getResultLinksOfQuery($query, $nbMaxResults, $APIKeyNumber + 1);
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
        
        if ($nbMaxResultsFood > 0) {
            foreach ($googleResultsJSONFood['items'] as $item)
            {
                $links[$item['link']] = array(
                    'title' => $item['title'],
                    'description' => $item['snippet']
                ); 
            }
        }
        
        if ($nbMaxResults == DEFAULT_NUMBER_OF_PAGES) {
            $cache->setResultListInModuleOne($query, $links);
        }
        
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