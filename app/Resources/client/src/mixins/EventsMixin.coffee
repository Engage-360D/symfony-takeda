EventsMixin =
  addEventListener: (element, event, callback) ->
    addEvent =  element.attachEvent or element.addEventListener
    addEvent event, callback
    
  triggerEvent: (element, name) ->
    if document.createEvent
      event = new Event name
      element.dispatchEvent event
    else
      event = document.createEventObject()
      element.fireEvent "on#{name}", event
  

module.exports = EventsMixin
