<?php

class TextExtractor
{

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
                    $text = self::getTextFromHtml($html);
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
        $textFromURL = file_get_contents($url);
        if($textFromURL == false)
            return null;
        return $textFromURL;
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
        return $text; // text
    }

    /*
    // Ebauche de parseur html plus evolue que strip tags
    
    public static function getTextFromHtml($htmlString)
    {
        // finalText will be returned
        $finalText = "";
        
        
        $finalText.=TextExtractor::getNodeVal($htmlString, 'h1') . '. ';
        $finalText.=TextExtractor::getNodeVal($htmlString, 'h2') . '. ';
        $finalText.=TextExtractor::getNodeVal($htmlString, 'h3') . '. ';
        $finalText.=TextExtractor::getNodeVal($htmlString, 'span');
        $finalText.=TextExtractor::getNodeVal($htmlString, 'div');
        $finalText.=TextExtractor::getNodeVal($htmlString, 'b');
        $finalText.=TextExtractor::getNodeAttr($htmlString, 'a', 'href');
        
        return $finalText;
    }
    
    public static function getNodeVal($htmlString, $node)
    {
        $finalText = '';
        
        // Create a DOM parser object
        $dom = new DOMDocument();
        // @ : to suppress warnings
        @$dom->loadHTML($htmlString);
        
        foreach($dom->getElementsByTagName($node) as $tag) {
            $text = $tag->nodeValue;
            $finalText .= $text . ' ';
        }
        return $finalText;
    }
    
    public static function getNodeAttr($htmlString, $node, $attr)
    {
        $finalText = '';
        
        // Create a DOM parser object
        $dom = new DOMDocument();
        // @ : to suppress warnings
        @$dom->loadHTML($htmlString);
        
        foreach($dom->getElementsByTagName($node) as $tag) {
            $text = $tag->getAttribute($attr);
            $finalText .= $text . ' ';
        }
        return $finalText;
    }*/
}