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
        $links = array();

        switch (SEACH_ENGINE) {
            case 'google':

                $googleResults = \Utils\Utils::CallAPI('GET', SEARCH_ENGINE_URL, array(
                    'key' => SEARCH_ENGINE_KEY,
                    'cx' => GOOGLE_SEARCH_CX,
                    'q' => $query
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