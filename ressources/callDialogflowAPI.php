<?php
    // Set the response as a JSON content with UTF-8
    header('Content-type: application/json; charset=utf-8');

    
    /**
     * Google Dialogflow V2 API Auth
     */

    // $curl = curl_init();
    // curl_setopt($curl, CURLOPT_URL, 'https://dialogflow.googleapis.com/v2beta1/projects/newagent-9a065/agent/sessions/a17a00bc-7062-cc30-c32a-8eb9847dfab8:detectIntent');
    // curl_setopt($curl, CURLOPT_POST, true);
    // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curl, CURLOPT_POSTFIELDS, '{
    //     "queryInput":{
    //         "text":{
    //             "text":"'.$dataMessage.'",
    //             "languageCode":"en"
    //         }
    //     },
    //     "queryParams":{
    //         "timeZone":"Europe/Paris"
    //     }
    // }');
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    // $headers = array(
    //     'Content-Type: application/json',
    //     'Authorization: Bearer ya29.c.Elq1BqmJuwmuK9q0vze62ih0iUMkYtVMEdSppUbCMknddSFbLuRjU-jSkFUmdtsZGPSgwDATez2vE2pl5FLf6eLt6VLikVsi0wUVxpkjYD_86Yg1s7OpJP8HwtY'
    // );
    // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    // $result = json_decode(curl_exec($curl));

    // curl_close($curl);
    // $result = $result->queryResult->fulfillmentText;


    /**
     * Google Dialogflow V1 API Auth
     */

    // Get config.ini (contains API Keys) file for security reasons
    $ini = parse_ini_file('../config/config.ini');

    // Function to call Google Dialogflow
    function dialogFlow($dataMessage)
    {
        // Get ini file as global var
        global $ini;

        // Build the right url with  the user's message
        $url = 'https://api.dialogflow.com/v1/query?';
        $url .= http_build_query([
            'v' => '20170712',
            'query' => $dataMessage,
            'lang' => 'en',
            'sessionId' => 'a17a00bc-7062-cc30-c32a-8eb9847dfab8',
            'timezone' => 'Europe/Paris'
        ]);

        // Put the API Key from config.ini file
        $headers = array(
            'Authorization: Bearer '.$ini['dialogflow_key']
        );

        // Curl the url
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // Decode the curl repsonse
        $result = json_decode(curl_exec($curl));

        // Get and return the text return by Google Dialogflow
        $result = $result->result->fulfillment->speech;
        return $result;
    };