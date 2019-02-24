<?php
    // Include for Curl function
    include 'ressources/getPokemonData.php';

    // Url to curl for pokemon's list
    $url = 'https://pokeapi.co/api/v2/pokemon/?';

    // Set the list's limit if limit customized by user or not
    if(empty($_POST['limit']))
    {
        $limit = 964;
    }
    else
    {
        $limit = $_POST['limit'];
    }

    // Url building
    $url .= http_build_query([
        'limit' => $limit
    ]);
    
    // Call curl function
    $result = dataCurl($url, $limit, 'listpokemons', './cache/');

    // Specific Curl function for pokemon's sprites
    function spritesRequest($specificUrl)
    {
        $id = substr($specificUrl, strpos($specificUrl, 'pokemon/') + 8);
        $result = 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' . str_replace('/', '', $id) . '.png';

        return $result;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-128961312-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-128961312-1');
    </script>
    <!-- Basic HTML Init -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Poké Messenger</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <link rel="stylesheet" href="./style/style.css">
    <link rel="icon" sizes="192x192" href="./images/logo192.png">
    <link rel="icon" sizes="512x512" href="./images/logo512.png">
    <link rel="apple-touch-icon" sizes="192x192" href="./images/logo192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="./images/logo512.png">
    <meta name="theme-color" content="#C62828">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="description" content="Choose your favortie pokémon and chat with PokéBot! You can say whatever you want to PokéBot. A question about a pokémon? Or you just want to talk to a bot about life and get his best jokes? PokéBot will answer right away!">
    <!-- Progressive Web App -->
    <link rel="manifest" href="./ressources/manifest.json">
    <!-- OpenGraph FB -->
    <meta property="og:url" content="https://francoisxaviermanceau.fr/lab/PokeAPI/"/>
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Poké Messenger"/>
    <meta property="og:description" content="Choose your favortie pokémon and chat with PokéBot! You can say whatever you want to PokéBot. A question about a pokémon? Or you just want to talk to a bot about life and get his best jokes? PokéBot will answer right away!"/>
    <meta property="og:image" content="https://francoisxaviermanceau.fr/lab/PokeAPI/images/share.jpg"/>
    <!-- Structured Data -->
    <script type="application/ld+json">
        {
          "@context": "http://schema.org/",
          "@type": "WebSite",
          "name": "Poké Messenger",
          "alternateName": "François-Xavier Manceau",
          "url": "http://poke-messenger.francoisxaviermanceau.fr"
        }
    </script>
</head>
<body>
    <main>
        <div class="friends-list-container">
            <div class="list-header">
                <img src="./images/settings.svg" alt="Number of pokemons" class="settings">
                <span>Poké Messenger</span>
                <img src="./images/left-arrow.svg" alt="Go to menu" class="menu-arrow">
            </div>
            <div class="search-bar">
                <input type="text" name="search-bar" id="search-bar" placeholder="Search a pokéfriend">
                <div class="list-settings">
                    <form action="#" method="post">
                        <input type="number" name="limit" id="settings" placeholder="How many pokemons to display?" min="0" max="964">
                        <input type="submit" value="OK" id="submit">
                    </form>
                </div>
            </div>
            <div class="friends-list">
                <ul>
                    <li class="poke-friends is-active" data-pokemon="François-Xavier Manceau">
                        <img src="./images/master.jpeg" alt="François-Xavier Manceau" class="image-master">
                        <div class="conversation-information">
                            <span class="name">François-Xavier Manceau</span>
                            <span class="message">Talk to the dev</span>
                        </div>
                    </li>
                    <?php foreach($result->results as $_pokemon): ?>
                    <li class="poke-friends" data-pokemon="<?=ucfirst($_pokemon->name) ?>">
                        <img src="<?=spritesRequest($_pokemon->url); ?>" alt="<?=str_replace('-', ' ', ucfirst($_pokemon->name)) ?>">
                        <div class="conversation-information">
                            <span class="name"><?=str_replace('-', ' ', ucfirst($_pokemon->name)) ?></span>
                            <span class="message">Send a message to <?=str_replace('-', ' ', ucfirst($_pokemon->name)) ?></span>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="conversation-container">
            <div class="conversation-header">
                <img src="./images/left-arrow.svg" alt="Go to menu" class="menu-arrow">
                <div class="pokemon-status">
                    <span class="name"><?=ucfirst($_pokemon->name) ?></span>
                    <span class="status">Active now</span>
                </div>
            </div>
            <div class="conversation-messages-container">
                <div class="conversation" data-pokemon="<?=$_pokemon->name ?>">
                    <span class="start-conversation">Say something to François-Xavier Manceau!</span>
                </div>
            </div>
            <div class="conversation-input-chat">
                <input type="text" name="chat-text" id="chat-text" placeholder="Write something, come on!">
                <img src="./images/send-button.svg" alt="send" class="send-img">
            </div>
        </div>
    </main>
    <script src="./script/script.js"></script>
</body>
</html>