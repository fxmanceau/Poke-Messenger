# Poké Messenger
> A custom Messenger based on PokéAPI with a bot answering questions about Pokemons, or just talking about life and jokes

![Mockup Poké Messenger](https://poke-messenger.francoisxaviermanceau.fr/images/mockup.gif)

## Features
- User friendly interface (because it's the Facebook Messenger one)
- Fully responsive (with side menu)
- Progressive Web App iOS & Android (Add the site to the homescreen of the Mobile Device) through manifest.json file
- Talk to a bot about life and jokes (Google Dialogflow API for natural conversation)
- Choose any conversation with a specific Pokemon to ask him questions
- Use of training.json file to guide curl function & data analyzastion
- Basic bot command (`!help` and `!pokemon`)
- Change conversation without loosing other conversation's messages
- Search in the search bar a specific Pokemon
- Change in the settings the limit to list more or less Pokemons in Friends list
- Handle random errors (images that cannot be loaded, no API connection...)
- Global cache that last 1h
- Security features (config.ini file for the API Keys + .htaccess)
- Google Analytics & SEO (JSON ld, OpenGraph contents...)

## Resources
- PokéAPI
- IBM Watson Natural Language Understanding API (to detect main themes of the questions)
- Google Dialogflow API (for custom Bot)

## Install
- Use a Web Server or MAMP/XAMP
- Or see [here](https://poke-messenger.francoisxaviermanceau.fr)


## Compatibility
#### Works on :
- [x] Chrome (Desktop and Mobile devices)
- [x] Firefox (Desktop and Mobile devices)
- [x] Opera (Desktop and Mobile devices)
- [x] Safari (Desktop and Mobile devices)
- [x] Edge (Desktop and Mobile devices)
