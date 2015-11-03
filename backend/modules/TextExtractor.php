<?php

class TextExtractor
{

    /**
     * List of accepted code in request
     */
    private static $include_header_code = array(
        '200'
    );

    /**
     * Get the text from every URL contained in UrlList
     * @param $urlList  array containing all the URL
     * @return Associative array Key = URL and Value = text from the URL
     */
    public static function getAllText($urlList)
    {
        $urlTextList = array();
        foreach($urlList as $url)
        {
            $clearUrl = stripslashes($url);
            $url_headers = @get_headers($clearUrl);
            $code_header = explode(' ', $url_headers[0])[1];

            if(in_array($code_header, self::$include_header_code) && !isset($urlTextList[$clearUrl]))
            {
                $html = self::getHtmlOfURL($clearUrl);
                $text = '';
                if($html != null)
                {
                    $text = self::getTextFromHtml($html);
                }
                $urlTextList[$clearUrl] = $text;
            }
        }
        return $urlTextList;
    }

    /**
     * Get the text from one single URL
     * @param $url just one URL
     * @return text from the URL
     */
    public static function getHtmlOfURL($url)
    {
        /*$textFromURL = file_get_contents($url);
        if($textFromURL == false)
        { 
            return null;
        }
        return $textFromURL;*/


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
    public static function getTextFromHtml($html)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);

        $text = '';
        $xpath = new DOMXPath($dom);
        $nodeList = $xpath->query('//div/text()|//h1/text()|//h2/text()|//h3/text()|//span/text()|//p/text()');

        $order   = array("\r\n", "\n", "\r");
        $replace = ' ';

        foreach($nodeList as $node)
        {
            if(!empty(trim($node->textContent)))
                $text .= str_replace($order, $replace, trim($node->textContent)) . ' ';
        }
        return $text;
    }

    public static function getAllTextTest() {
        // getAllText (texte final récupéré pour toutes les url)
        return self::getAllText(array('http://allrecipes.com/recipe/7281/chocolate-cherry-cake-i/', 'http://www.pillsbury.com/recipes/chocolate-cherry-bars/15d6f3ce-21b3-43fb-8cb0-b33fb4177d3e'));

        // getTextFromHtml (texte final récupéré pour une seule page html)
        //return self::getTextFromHtml('html');

        // getHtmlOfUrl
        //return self::getHtmlOfUrl('http://allrecipes.com/recipe/7281/chocolate-cherry-cake-i/');
    }

}