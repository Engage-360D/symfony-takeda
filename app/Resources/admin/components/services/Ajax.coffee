$ = require "jquery"
require "jStorage"
require "JSOjs"

class Ajax
  constructor: ->

  get: (url, callback) ->
    $.oajax
      url: url
      jso_allowia: true
      jso_provider: "engage360d"
      dataType: "json"
      success: callback

  post: (url, data, callback) ->
    $.oajax
      url: url
      type: "POST"
      jso_provider: "engage360d"
      jso_allowia: true
      dataType: "json"
      data: data
      success: callback

  put: (url, data, callback) ->
    delete data.id if data.id
    $.oajax
      url: url
      type: "PUT"
      jso_provider: "engage360d"
      jso_allowia: true
      dataType: "json"
      data: data
      success: callback

  remove: (url, callback) ->
    $.oajax
      url: url
      method: "DELETE"
      jso_provider: "engage360d"
      jso_allowia: true
      dataType: "json"
      success: callback

module.exports = new Ajax()
