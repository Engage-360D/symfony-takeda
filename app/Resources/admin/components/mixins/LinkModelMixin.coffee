ReactLink = require "react/lib/ReactLink"


LinkModelMixin =
  linkModel: (key) ->
    model = @model
    new ReactLink model.get(key), (value) ->
      @value = value
      model.set key, value


module.exports = LinkModelMixin
