Backbone = require "backbone"
Backbone.$ = require "jquery"


Model = Backbone.Model.extend
  prepareField: (value, configs) ->
    value = configs.formatter value if configs.formatter
    value

  prepare: (data) ->
    return data unless @fields
    prepared = {}
    fields = @fields or {}
    for field, value of data
      if fields[field]
        if fields[field].persist isnt false
          prepared[field] = @prepareField value, fields[field] 
      else
        prepared[field] = value
    prepared

  convertField: (value, configs) ->
    value = configs.converter value if configs.converter
    value

  convert: (data) ->
    return data unless @fields
    converted = {}
    fields = @fields or {}
    for field, value of data
      if fields[field]
        converted[field] = @convertField value, fields[field] 
      else
        converted[field] = value
    converted

  toJSON: (options) ->
    @prepare Backbone.Model.prototype.toJSON.call @

  parse: (resp, options) ->
    @convert resp

module.exports = Model
