<?php

/**
 * Static class with methods to extract text of HTML page from an URL
 */
class TextExtractor
{

    /**
     * List of accepted code in request
     */
    private static $include_header_code = array('200');

    /**
     * Get the text from every URL contained in UrlList
     * @param $urlList  array containing all the URL
     * @return Associative array Key = URL and Value = text from the URL
     */
    public static function getAllText($urlList)
    {
        $cache = Cache::getInstance();

        $urlTextList = array();
        foreach($urlList as $url)
        {
            $clearUrl = stripslashes($url);
            
            $textOfUrlCached = $cache->getSingleResultOfModuleTwoByUrl($clearUrl);
        
            if ($textOfUrlCached)
            {
                $urlTextList[$clearUrl] = $textOfUrlCached;
            }
            else
            {

                $url_headers = @get_headers($clearUrl);
                $code_header = explode(' ', $url_headers[0])[1];
    
                if(in_array($code_header, self::$include_header_code) && !isset($urlTextList[$clearUrl]))
                {
                    $html = self::getHtmlOfURL($clearUrl);

                    if($html != null)
                    {
                        $urlTextList[$clearUrl] = self::getTextFromHtml($html);
                        
                        $cache->setSingleResultInModuleTwo($clearUrl, $urlTextList[$clearUrl]);
                    }
                }

            }
        }
        return $urlTextList;
    }

    /**
     * Get the text from one single URL
     * @param $url just one URL
     * @return text from the URL
     */
    private static function getHtmlOfURL($url)
    {
        $ch = curl_init();
        $timeout = 1;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;
    }

    /**
     * Get relevent text from an Html page
     * @param $html the html page
     * @return string the relevent text from html page
     */
    private static function getTextFromHtml($html)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);

        $text = '';
        $xpath = new DOMXPath($dom);
        $nodeList = $xpath->query('//div/text()|//h1/text()|//h2/text()|//h3/text()|//span/text()|//p/text()');

        $toReplace   = array("\r\n", "\n", "\r");

        foreach($nodeList as $node)
        {
            if(!empty(trim($node->textContent)))
            {
                $text .= str_replace($toReplace, ' ', trim($node->textContent));
                if (in_array(substr($text, -1), array( ',', '.', ';', '!', '?')))
                {
                    $text .= ' ';
                }
                else if (!in_array(substr($text, -2), array( ',', '.', ';', '!', '?')))
                {
                    $text .= '. ';
                }
            }

        }
        return $text;
    }

    /**
     * Get all text of tests urls (used to test this module)
     */
    public static function getAllTextTest() {
        return self::getAllText(
            array(
                'http://allrecipes.com/recipe/7281/chocolate-cherry-cake-i/',
                'http://www.pillsbury.com/recipes/chocolate-cherry-bars/15d6f3ce-21b3-43fb-8cb0-b33fb4177d3e',
                'http://allrecipes.com/recipe/39846/tomato-cold-soup-with-parmesan-cheese-ice-cream/'
                )
            );
    }

}