$ = require "jquery"


EventsMixin =
  addEventListener: (element, event, callback) ->
    $(element).on event, callback
    
  triggerEvent: (element, name) ->
    $(element).trigger name
  

module.exports = EventsMixin
