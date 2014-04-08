Backbone = require "backbone"
Backbone.$ = require "jquery"


Collection = Backbone.Collection.extend
  toRawJSON: ->
    @map (model) -> model.attributes

module.exports = Collection
