<?php

class Utils
{
    // Method: POST, PUT, GET etc
    // Data: array("param" => "value") ==> index.php?param=value
    /**
     * 
     */
    public static function CallAPI($method, $url, $data = false, $outputFormat = 'application/json')
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data) 
                {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
                
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
                
            default:
                if ($data)
                {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }
        
        // Set the header with the output format
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: ' . $outputFormat)); 

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }



}