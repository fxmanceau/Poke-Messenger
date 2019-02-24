/**
 * Init variables required
 */

// Friends list elements
const $searchBar = document.querySelector('#search-bar')
const $listElements = document.querySelectorAll('.poke-friends')

// Conversation elements
const $textArea = document.querySelector('#chat-text')
const $conversationContainer = document.querySelector('.conversation-messages-container')
const $messagesContainer = $conversationContainer.querySelector('.conversation')
const $sendImg = document.querySelector('.send-img')

// Side menu elements
const $buttonMenu = document.querySelectorAll('img.menu-arrow')
const $menu = document.querySelector('.friends-list-container')

// Elements to change when changing conversation
let $activeConversation = document.querySelector('.poke-friends.is-active')
const $pokefriendsList = $menu.querySelectorAll('li.poke-friends')
const $headerConversation = document.querySelector('.conversation-container')
const $headerConversationName = $headerConversation.querySelector('.pokemon-status .name')
const $startConversation = $conversationContainer.querySelector('.start-conversation')

// Settings elements
const $settingsButton = $menu.querySelector('.settings')
const $settingsForm = $menu.querySelector('.list-settings')

// All the images in DOM
const $imagesDOM = document.querySelectorAll('img')

// Messages history array
let messagesHistory=[]


/**
 * Create classes required
 */

// Search bar class
class SearchBar
{
    constructor(searchBar, listElements)
    {
        this.searchBar = searchBar
        this.listElements = listElements
        this.searchEngine()
        this.triggerUnfocus()
    }

    // The search engine
    searchEngine()
    {
        // When pressing a key, looking for data-set match
        this.searchBar.addEventListener('keyup', () =>
        {
            // Checking if value in search bar input and if it contains characters
            if(this.searchBar.value && this.searchBar.value!='')
            {
                for(let i=0; i < this.listElements.length; i++)
                {
                    const datasearch = (this.listElements[i].dataset.pokemon).toLowerCase()
                    if(!datasearch.indexOf((this.searchBar.value).toLowerCase()))
                    {
                        // if match, display the list elements
                        this.listElements[i].style.display = "flex"
                    }
                    else
                    {
                        // if no match, masking the elements 
                        this.listElements[i].style.display = "none"
                    }
                }
            }
            else
            {
                // if no characters, display every list elements
                for(let i=0; i < this.listElements.length; i++)
                {
                    this.listElements[i].style.display = "flex"
                }
            }
        })
    }

    // Handleing the unfocus of the search bar
    unfocus()
    {
        // Elements to display when unfocus happen
        for(let i=0; i < this.listElements.length; i++)
        {
            this.listElements[i].style.display = "flex"
        }
        this.searchBar.value=''
    }
    triggerUnfocus()
    {
        // Listen click on friends list elements 
        for(let i=0; i < this.listElements.length; i++)
        {
            // Default devices
            this.listElements[i].addEventListener('click', () =>
            {
                // Trigger unfocus function
                this.unfocus()
            })

            // Touch & mobile devices
            this.listElements[i].addEventListener('touchleave', () =>
            {
                // Trigger unfocus function
                this.unfocus()
            })
        }
    }
}
const searchEngine = new SearchBar($searchBar, $listElements)

/**
 * Class for the conversation
 */
class Discussion
{
    constructor(textArea, conversationContainer, messagesContainer, imgButton)
    {
        this.textArea = textArea
        this.conversationContainer = conversationContainer
        this.messagesContainer = messagesContainer
        this.imgButton = imgButton
        
        this.triggerCreateMessage()
        this.autoScroll()
        this.autoMessage()
    }

    // Generation of messages into the DOM
    generateDOMMessage(messageFrom, messageFromPokemonData, activePokemon)
    {
        // Checking if it's a user message...
        if(messageFrom === 'user' && activePokemon !== 'loaded')
        {
            // Generation user span message with his elements, CSS class & text
            this.userMessage = document.createElement('div')
            this.userMessage.classList.add('message', 'message-user')
            this.messagesContainer.appendChild(this.userMessage)
            this.spanMessage = document.createElement('span')
            this.userMessage.appendChild(this.spanMessage)
            this.spanMessage.innerHTML = this.textArea.value
            this.textArea.value = ''
        }

        // Or a pokemon message
        else if(messageFrom === 'pokemon')
        {
            // Generation pokemon span message with his elements, CSS class & text
            this.messagesContainer.querySelector('.message-pokemon.loading').remove()
            this.pokemonMessage = document.createElement('div')
            this.pokemonMessage.classList.add('message', 'message-pokemon')
            this.messagesContainer.appendChild(this.pokemonMessage)
            this.pokemonImage = document.createElement('img')
            this.pokemonImage.src = $activeConversation.querySelector('img').src
            this.pokemonMessage.appendChild(this.pokemonImage)
            this.spanMessage = document.createElement('span')
            this.pokemonMessage.appendChild(this.spanMessage)
            this.spanMessage.innerHTML = messageFromPokemonData
        }

        // Auto scroll when poping a new messaage
        this.conversationContainer.scrollTo(0, this.messagesContainer.offsetHeight)
    }

    // Display a waiting span with moving dots
    waitingAnimation()
    {
        // Generate the waiting span 
        this.pokemonMessage = document.createElement('div')
        this.pokemonMessage.classList.add('message', 'message-pokemon', 'loading')
        this.messagesContainer.appendChild(this.pokemonMessage)
        this.pokemonImage = document.createElement('img')
        this.pokemonImage.src = $activeConversation.querySelector('img').src
        this.pokemonMessage.appendChild(this.pokemonImage)
        this.spanMessage = document.createElement('span')
        this.spanMessage.classList.add('loading-span')
        this.pokemonMessage.appendChild(this.spanMessage)

        // Put 3 dots in the span
        for(let i=0; i<3; i++)
        {
            this.spanDot = document.createElement('div')
            this.spanDot.classList.add('loading-dots', `loading-dot-${i}`)
            this.spanMessage.appendChild(this.spanDot)
        }

        // Auto scroll when poping the waiting span
        this.conversationContainer.scrollTo(0, this.messagesContainer.offsetHeight)
    }

    // Trigger the user creation message when sending it
    triggerCreateMessage()
    {
        // Trigger with key press
        this.textArea.addEventListener('keypress', (_event) =>
        {
            if(_event.key == 'Enter' && this.textArea.value!='')
            {
                // Variables needed to call the Fetch APIs function
                const activePokemon = ($activeConversation.dataset.pokemon).toLowerCase()
                const message = this.textArea.value

                // Call function to Fetch APIs
                this.callAPIs(activePokemon, message, true)
            }
        })

        // Trigger with click
        this.imgButton.addEventListener('click', () => {
            if(this.textArea.value!='')
            {
                // Variables needed to call the Fetch APIs function
                const activePokemon = ($activeConversation.dataset.pokemon).toLowerCase()
                const message = this.textArea.value

                // Call function to Fetch APIs
                this.callAPIs(activePokemon, message, true)
            }
        })
    }

    // Auto scroll function for window resize
    autoScroll()
    {
        window.addEventListener('resize', () => this.conversationContainer.scrollTo(0, this.messagesContainer.offsetHeight))
    }

    // Fetch APIs function
    callAPIs(activePokemon, message, timeOut)
    {
        // Fetch the PHP script to send the user message and get a repsonse
        window
        .fetch(
            `./ressources/callPokeAPI.php?pokemon=${activePokemon}&message=${message}`,

            // On page load, generate user message in DOM
            this.generateDOMMessage('user', null, activePokemon),
            // Trigger wiating span
            this.waitingAnimation()
        )
        .then((_response) =>
        {
            return _response.json()
        })
        .then((_result) =>
        {
            let pokemonAnswer = ''

            // Check if bot response is empty or doesn't exist
            if(!JSON.stringify(_result) || JSON.stringify(_result) == '')
            {
                pokemonAnswer = 'I am truly sorry, I did not understand your question!'
            }
            // If exists and is not empty, display bot repsonse
            else
            {
                pokemonAnswer = `${JSON.parse(JSON.stringify(_result))}`
            }

            // Trigger de generation in DOM immediatly the response or wait, controled by timeOut
            if(timeOut)
            {
                setTimeout(() => {
                    this.generateDOMMessage('pokemon', pokemonAnswer, activePokemon)
                }, (Math.random() * (2000 - 200) + 200))
            }
            else
            {
                this.generateDOMMessage('pokemon', pokemonAnswer, activePokemon)
            }
            
        })
        // If error, display a default message
        .catch(() => {
            if(timeOut)
            {
                setTimeout(() => {
                    this.generateDOMMessage('pokemon', 'I am truly sorry, I did not understand your question!')
                }, (Math.random() * (2000 - 200) + 200))
            }
            else
            {
                this.generateDOMMessage('pokemon', 'I am truly sorry, I did not understand your question!')
            }
            
        })
    }
    
    // When document loaded, display the !help bot message
    autoMessage()
    {
        window.addEventListener('DOMContentLoaded', () =>
        {
            this.callAPIs('loaded', '!help', false)
        })
    }
}
const send = new Discussion($textArea, $conversationContainer, $messagesContainer, $sendImg, $activeConversation)

class Menu
{
    constructor(buttonMenu, menu)
    {
        this.buttonMenu = buttonMenu
        this.menu = menu
        this.toggleMenu()
    }
    toggleMenu()
    {
        for(let i=0; i<this.buttonMenu.length; i++)
        {
            this.buttonMenu[i].addEventListener('click', () =>
            {
                this.menu.classList.toggle('is-active')
            })
        }
    }
}
const clickableMenu = new Menu($buttonMenu, $menu)

//Change pokemon's conversation class
class ChangeConversation
{
    constructor(pokefriendsList, conversationContainer, activeConversation, headerConversationName, startConversation, menu, messagesHistory)
    {
        this.menu = menu

        this.pokefriendsList = pokefriendsList
        this.conversationContainer = conversationContainer
        this.activeConversation = activeConversation
        this.headerConversationName = headerConversationName
        this.startConversation = startConversation

        this.messagesHistory = messagesHistory
        this.pokemonName = ($activeConversation.dataset.pokemon).toLowerCase()

        this.changerTrigger()
        this.nameConversation()
    }

    // Change the conversation name in the conversation header
    nameConversation()
    {
        this.headerConversationName.innerHTML = this.activeConversation.dataset.pokemon
        this.startConversation.innerHTML = `Say something to ${this.activeConversation.dataset.pokemon}`
        this.pokemonName = this.activeConversation.dataset.pokemon
    }

    // Trigger the change
    changerTrigger()
    {
        // Trigger the change by listening friends list elements
        for(let i=0; i<this.pokefriendsList.length; i++)
        {
            this.pokefriendsList[i].addEventListener('click', () =>
            {
                conversationChanger(i)
            })
            this.pokefriendsList[i].addEventListener('touchsleave', () =>
            {
                conversationChanger(i)
            })
        }
        // conversation changer function
        const conversationChanger = (i) =>
        {
            // Gettings conversation messages and saving them in a messagesHistory array
            this.messages = this.conversationContainer.querySelectorAll('.conversation .message')
            this.messagesHistory[this.pokemonName] = this.messages

            // Change the active conversation in friends list
            this.activeConversation.classList.remove('is-active')
            this.pokefriendsList[i].classList.add('is-active')

            this.activeConversation = this.pokefriendsList[i]
            $activeConversation = this.pokefriendsList[i]
            
            // Remove every messages nodes
            for(let k=0; k<this.messages.length; k++)
            {
                this.messages[k].remove()
            }
            this.nameConversation()
            this.closeMenu()
            this.historyMessages()
        }
    }
    
    // Restoring saved messages
    historyMessages()
    {
        // Check if there is messages for a specific conversation in the messages history array
        if(this.messagesHistory[this.pokemonName])
        {
            this.conversation = this.conversationContainer.querySelector('.conversation')

            // Display them in the conversation
            for(let i=0; i<this.messagesHistory[this.pokemonName].length; i++)
            {
                this.conversation.appendChild(this.messagesHistory[this.pokemonName][i])
            }
        }
    }

    // Close the menu when changing conversation
    closeMenu()
    {
        if(window.innerWidth < 850)
        {
            this.menu.classList.toggle('is-active')
        }
    }
}
const changeConversationTrigger = new ChangeConversation($pokefriendsList, $conversationContainer, $activeConversation, $headerConversationName, $startConversation, $menu, messagesHistory)


// Display the input to change limit of pokemons to display 
class ChangeLimit
{
    constructor(settingsButton, settingsForm)
    {
        this.settingsButton = settingsButton
        this.settingsForm = settingsForm
        this.toggleSettings()
    }
    // Open or close the input when clicking on the settings icon
    toggleSettings()
    {
        this.settingsButton.addEventListener('click', () =>
        {
            this.settingsForm.classList.toggle('is-active')
        })
    }
}
const changeLimit = new ChangeLimit($settingsButton, $settingsForm)

// Default image if images loading fails
class ImgLoadError
{
    constructor(imagesDOM)
    {
        this.imagesDOM = imagesDOM
        this.replaceSrc()
    }
    // Replace images sources
    replaceSrc()
    {
        for(let i=0; i<this.imagesDOM.length; i++)
        {
            // Listen to the error
            this.imagesDOM[i].addEventListener('error', () =>
            {
                // Change the images src for the default one
                this.imagesDOM[i].onnerror = this.imagesDOM[i].src = "./images/logo192.png"
                this.imagesDOM[i].classList.add('error')
            })
        }
    }
}
const imgNotLoaded = new ImgLoadError($imagesDOM)