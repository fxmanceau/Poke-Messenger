<?php
    // Function to curl a custom url with custom cache path
    function dataCurl($url, $pokemonName, $cacheName, $path=null)
    {
        // Create cache info
        $cacheKey = md5($cacheName);
        if($path)
        {
            $cachePath = $path.md5($pokemonName).'_'.$cacheKey;
        }
        else
        {
            $cachePath = '../cache/'.md5($pokemonName).'_'.$cacheKey;
        }

        // Check if cache file exists and is not too old
        if(file_exists($cachePath) && time() - filemtime($cachePath) < 3600)
        {
            // No curl needed, return the cache
            $result = json_decode(file_get_contents($cachePath));

            return $result;
        }

        else
        {   
            // Curl needed, cache creation needed

            // Curl with custom url
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($curl);
            curl_close($curl);
            
            // Create cache file with custom name
            file_put_contents($cachePath, $result);

            // Return curl response
            return $result=json_decode($result);
        };
    };


    // Function to get the right pokemon's data
    function dataPokemon($fromSection, $dataPath)
    {
        // Set the response as a JSON content with UTF-8
        header('Content-type: application/json; charset=utf-8');

        // Get necessary global variables
        global $dataPokemon;
        global $trainingData;
        global $url;

        // Curl the data to analyze according the the pokemon name
        $dataToAnalyze = dataCurl($url, $dataPokemon, 'global');

        /**
         * EVERYTHING NEXT NEED AND IS ACCORDING TO THE TRAINING FILE
         */

        // If enable is true, analyze data for returning a response
        if($dataPath->$fromSection->enable)
        {

            // If we want to analyze an array or a string with an url to curl
            if(is_array($dataToAnalyze->$fromSection) || (is_string($dataToAnalyze->$fromSection) && $dataPath->$fromSection->followUrl))
            {
                // If the array has an url to curl
                if($dataPath->$fromSection->followUrl && $dataPath->$fromSection->array==true)
                {
                    // Set the template bot answer 
                    $resultValues = $dataPath->$fromSection->responseTemplate;

                    // For each element of the array
                    foreach ($dataToAnalyze->$fromSection as $key => $_data)
                    {
                        // Set the curl path based on the url contained in the data analyzed and guided with the training file
                        $curlPath = $dataPath->$fromSection->url;

                        // If the curl path is not null or empty
                        if(!$curlPath == null || !$curlPath === '')
                        {
                            // Call Curl function with $curlPath as the custom url and custom cache name
                            $newData = dataCurl($_data->$curlPath->url, $dataPokemon, $fromSection.$key);
                        }
                        
                        else
                        {
                            // Else, call Curl function with the url contained in the data analyzed
                            $newData = dataCurl($_data->url, $dataPokemon, $fromSection.$key);
                        };
                    
                        // Set the final path and final target according to the training file
                        $finalPath = $dataPath->$fromSection->path;
                        $finalTarget = $dataPath->$fromSection->target;

                        // If final path exists
                        if($finalPath)
                        {
                            // And if it's an array
                            if(is_array($newData->$finalPath))
                            {
                                $resultValues .= '<br><br>';
                                foreach ($newData->$finalPath as $_data)
                                {
                                    // Build the final result step by step
                                    $resultValues .=  '&emsp;<div class="dot"></div> ' . ucfirst($_data->$finalTarget);
                                }
                            }

                            // Else, build the result directly
                            else
                            {
                                $resultValues .= ucfirst($newData->$finalTarget);
                            }
                        }

                        // Else if path doesn't exist 
                        else
                        {
                            // And if it's an array
                            if(is_array($newData))
                            {
                                $resultValues .= '<br><br>';
                                foreach ($newData as $_data)
                                {
                                    // Build the final result step by step
                                    $resultValues .= '&emsp;<div class="dot"></div> ' . ucfirst($_data->$finalTarget);
                                }
                            }
                            else
                            {
                                // Else, build the result directly
                                $resultValues .= ucfirst($newData->$finalTarget);
                            }
                        }
                    }

                    // Remove the - in the result
                    $finalResult = str_replace('-', ' ', $resultValues);
                }
            
                // Else, if we want to analyze a url and there is no array (direct path)
                else if($dataPath->$fromSection->followUrl && $dataPath->$fromSection->array==false)
                {
                    // Set the curl path based on the url contained in the data analyzed and guided with the training file
                    $curlPath = $dataPath->$fromSection->url;

                    // Call Curl function with $curlPath as the custom url and custom cache name
                    $newData = dataCurl($dataToAnalyze->$curlPath, $dataPokemon, $fromSection);
                
                    // Set the final path and final target according to the training file
                    $finalPath = $dataPath->$fromSection->path;
                    $finalTarget = $dataPath->$fromSection->target;

                    // If newData is an array
                    if(is_array($newData))
                    {
                        // Set the template bot answer 
                        $resultValues = $dataPath->$fromSection->responseTemplate . '<br>';

                        // If newData is empty
                        if(empty($newData))
                        {
                            // Return a default answer
                            $finalResult = 'Damn! I don\'t have any information for you... 😥';
                        }

                        else
                        {
                            // Build the final result step by step
                            foreach ($newData as $_data)
                            {
                                $resultValues .= '&emsp;<div class="dot"></div> ' . ucfirst($_data->$finalPath->$finalTarget)  . '<br>';
                            }

                            // Remove the - in the result
                            $finalResult = str_replace('-', ' ', $resultValues);
                        }
                    }
                }
            
                // Else, if there is no url to curl and we want to analyze an array
                else if(!$dataPath->$fromSection->followUrl && $dataPath->$fromSection->array)
                {
                    // Set the final path and final target according to the training file
                    $finalPath = $dataPath->$fromSection->path;
                    $finalTarget = $dataPath->$fromSection->target;

                    // Set the template bot answer 
                    $resultValues = $dataPath->$fromSection->responseTemplate . ': <br>';

                    // Build the final
                    foreach ($dataToAnalyze->$fromSection as $_data)
                    {
                        // If there are a second target to put in the result
                        if($dataPath->$fromSection->targetOption)
                        {
                            $optionsValues = '';

                            // Build the sub-result step by step
                            foreach ($dataPath->$fromSection->targetOption as $key => $_option)
                            {
                                $optionsValues .= $dataPath->$fromSection->targetOptionResponseTemplate[$key] . $_data->$_option . ' ';
                            }
                            $resultValues .= '&emsp;<div class="dot"></div> ' . ucfirst($_data->$finalPath->$finalTarget) . ': ' . $optionsValues . '<br>';
                        }

                        // Else, build the final result step by step
                        else
                        {
                            $resultValues .= '&emsp;<div class="dot"></div> ' . ucfirst($_data->$finalPath->$finalTarget) . '<br>';
                        }

                    }

                    // Remove the - in the result
                    $finalResult = str_replace('-', ' ', $resultValues);
                }
            }

            // Else, if the data is an string with no url to curl, or a numeric
            else if((is_string($dataToAnalyze->$fromSection) && !$dataPath->$fromSection->followUrl) || is_numeric($dataToAnalyze->$fromSection))
            {
                // Build the final result directly with the template bot answer and remove - in the result
                $finalResult = str_replace('-', ' ', $dataPath->$fromSection->responseTemplate . $dataToAnalyze->$fromSection);
            }

            // Else, if the data is an object
            else if((is_object($dataToAnalyze->$fromSection)))
            {
                // Set the final path and final target according to the training file
                $finalPath = $dataPath->$fromSection->path;
                $finalTarget = $dataPath->$fromSection->target;

                // Build the final result directly with the template bot answer and remove - in the result
                $finalResult = str_replace('-', ' ', $dataPath->$fromSection->responseTemplate . $dataToAnalyze->$fromSection->$finalTarget);
            }
        }

        // Else, return that the bot is a Secret Agent, FUCK YEAH
        else
        {
            $finalResult = 'Sorry, I\'m a Secret Agent 🕵️ and that information is classified!';
        }

        // Return the final result
        return $finalResult;
    }