<?php

class TextExtractor
{

    /**
    * Get the text from every URL contained in UrlList
    * @param $urlList  array containing all the URL
    * @return Associative array Key = URL and Value = text from the URL
    */
    public static function getAllText($urlList)
    {
        $urlText = array();
        foreach($urlList as $key)
        {
            $valueHtml = getTextOfURL($key);
            //$valuePlain = parseHTML($valueHtml);
            $valuePlain = strip_tags($valueHtml);
            
            $allURL[$key] = $valuePlain;
        }
        var_dump($allURL);
        return $allURL;
    }

    /**
    * Get the text from one single URL
    * @param $url just one URL
    * @return text from the URL
    */
    public static function getTextOfURL($url)
    {
        $textFromURL = file_get_contents($url);
        return $textFromURL;
    }
    
    // Ebauche de parseur html plus evolue que strip tags
    /*
    function parseHTML($htmlString)
    {
        // finalText will be returned
        $finalText = "";
        
        
        $finalText.=getNodeVal($htmlString, 'h1');
        $finalText.=getNodeVal($htmlString, 'h2');
        $finalText.=getNodeVal($htmlString, 'h3');
        $finalText.=getNodeVal($htmlString, 'span');
        $finalText.=getNodeVal($htmlString, 'div');
        $finalText.=getNodeVal($htmlString, 'b');
        $finalText.=getNodeAttr($htmlString, 'a', 'href');
        
        return $finalText;
    }
    
    function getNodeVal($htmlString, $node)
    {
        $finalText = "";
        
        // Create a DOM parser object
        $dom = new DOMDocument();
        // @ : to suppress warnings
        @$dom->loadHTML($htmlString);
        
        foreach($dom->getElementsByTagName($node) as $tag) {
                // display the titles
                $text = $tag->nodeValue;
                $finalText .= $text . "<br />";
        }
        return $finalText;
    }
    
    function getNodeAttr($htmlString, $node, $attr)
    {
        $finalText = "";
        
        // Create a DOM parser object
        $dom = new DOMDocument();
        // @ : to suppress warnings
        @$dom->loadHTML($htmlString);
        
        foreach($dom->getElementsByTagName($node) as $tag) {
                // display the titles
                $text = $tag->getAttribute($attr);
                $finalText .= $text . "<br />";
        }
        return $finalText;
    }
    */
}