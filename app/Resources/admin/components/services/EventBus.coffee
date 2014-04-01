events = require "events"

class EventBus extends events.EventEmitter
  constructor: ->
  getEvents: ->

module.exports = new EventBus()
