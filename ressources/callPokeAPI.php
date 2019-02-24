<?php
    // Require the differents call APIs PHP scripts
    require 'callWatsonAPI.php';
    require 'callDialogflowAPI.php';
    require 'getPokemonData.php';
    
    // Set the response as a JSON content with UTF-8
    header('Content-type: application/json; charset=utf-8');

    // Get the pokemon of the conversation & the user message
    $dataPokemon = $_GET['pokemon'];
    $dataMessage = $_GET['message'];

    // Load training JSON file to guide the algorithm
    $trainingFile = file_get_contents('../NLU/training.json');
    $trainingData = json_decode($trainingFile);

    // Call IBM Watson API with the user message to detect message's themes
    $result = watsonAPI($dataMessage);

    // If the response is an error, call Google Dialogflow API with user message
    if($result == 'error')
    {
        // Echo the JSON response
        echo json_encode(dialogFlow($dataMessage));
    }
    else
    {
        $response = '';
        
        // Repeat for every themes
        foreach ($result as $index => $_keywords)
        {
            // Set necessary variables
            $section = $_keywords->text;
            $url = 'https://pokeapi.co/api/v2/pokemon/'.$dataPokemon;

            // Check if theme is in the training files as a singular word
            if(in_array($section, $trainingData->pokeapiSections->researchWord->singular))
            {
                // If the pokemon is me, echo a default response
                if($dataPokemon === 'françois-xavier manceau')
                {
                    $response = 'Hey! I\'m not a Pokemon!';
                }

                else
                {
                    // Getting the theme occurence's key in the trainging file
                    $key = array_search($section, $trainingData->pokeapiSections->researchWord->singular);

                    // Set the right path
                    $fromSection = $trainingData->pokeapiSections->sections->sectionsAPI[$key];
                    $dataPath = $trainingData->dataPath;
                    
                    // Call the dataPokemon algorithm to get right data response
                    $response .= dataPokemon($fromSection, $dataPath);
                }
            }
            // Check if theme is in the training files as a plural word
            else if(in_array($section, $trainingData->pokeapiSections->researchWord->plural))
            {
                // If the pokemon is me, echo a default response
                if($dataPokemon === 'François-Xavier Manceau')
                {
                    $response = 'Hey! I\'m not a Pokemon!';
                }

                else
                {
                    // Getting the theme occurence's key in the trainging file
                    $key = array_search($section, $trainingData->pokeapiSections->researchWord->plural);

                    // Set the right path
                    $fromSection = $trainingData->pokeapiSections->sections->sectionsAPI[$key];
                    $dataPath = $trainingData->dataPath;
    
                    // Call the dataPokemon algorithm to get right data response
                    $response .= dataPokemon($fromSection, $dataPath);
                }
            }

            // If theme not in the training file, call Google Dialogflow function
            else
            {
                $response .= dialogFlow($dataMessage);
            }
            
            // Add <br> to the response to seperate each theme's response
            if($index < (count((array)$result)-1))
            {
                $response .= '<br><br>';
            }
        };

        // Echo the JSON response
        echo json_encode($response);
    };