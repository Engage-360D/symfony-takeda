RouteRecognizer = require "route-recognizer"
events = require "events"


class Router extends events.EventEmitter
  constructor: ->
    @setMaxListeners 20
    @history = history
    @routeRecognizer = new RouteRecognizer()

    window.addEventListener "hashchange", @recognize
    @recognize()

  recognize: =>
    result = @routeRecognizer.recognize document.location.hash.replace /^#!/, ""
    @emit "change", result if result

  add: (route) =>
    @routeRecognizer.add route

  state: ->
    document.location.hash

  back: ->
    @history.back()

  forward: ->
    @history.forward()

  handle: (route) =>
    document.location.hash = route

module.exports = new Router()
