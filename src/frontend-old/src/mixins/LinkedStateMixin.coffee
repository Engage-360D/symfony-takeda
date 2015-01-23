ReactStateSetters = require "react/lib/ReactStateSetters"
ReactLink = require "react/lib/ReactLink"


LinkedStateMixin =
  linkState: (key, callback) ->
    setter = ReactStateSetters.createStateKeySetter @, key

    new ReactLink @state[key], (value) ->
      setter value
      callback value if callback


module.exports = LinkedStateMixin
