<?php
    // Set the response as a JSON content with UTF-8
    header('Content-type: application/json; charset=utf-8');

    // Get config.ini (contains API Keys) file for security reasons
    $ini = parse_ini_file('../config/config.ini');

    // Function to call IBM Watson API
    function watsonAPI($dataMessage)
    {
        // Get ini file as global var
        global $ini;

        // Set up the curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://gateway-lon.watsonplatform.net/natural-language-understanding/api/v1/analyze?version=2018-03-19');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // Put user message & keywords (as a feature to analyze by Watson)
        curl_setopt($curl, CURLOPT_POSTFIELDS, '{
            "text":"'.$dataMessage.'",
            "features":
            {
                "keywords":{}
            }
        }');

        // Put the API Key from config.ini file
        curl_setopt($curl, CURLOPT_USERPWD, 'apikey:'.$ini['watson_key']);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    
        $headers = array(
            'Content-Type: application/json',
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
        // Decode the curl response
        $result = json_decode(curl_exec($curl));
    
        curl_close($curl);
    
        // Check if there are keywords or not
        if(!isset($result->keywords) || empty($result->keywords))
        {
            // If not, return an error message
            $result = "error";
        }
        else
        {
            // else, return an object with keywords in it
            $result = json_encode($result->keywords);
            $result = (object)json_decode($result);
        }
        return $result;
    };