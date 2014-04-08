Model = require "Engage360d/data/Model"
require "moment"


User = Model.extend
  urlRoot: "/api/users"

  fields:
    id:
      persist: false
    birthday:
      formatter: (value) ->
        value and value.format "YYYY-MM-DD"
      converter: (value) ->
        value and moment value
    enabled:
      persist: false
    username:
      persist: false
    lastLogin:
      persist: false
    lastname:
      persist: false
    facebookId:
      persist: false
    facebookAccessToken:
      persist: false
    vkontakteId:
      persist: false
    vkontakteAccessToken:
      persist: false
    registration:
      persist: false
    testResults:
      persist: false
    confirmInformation:
      persist: false

  url: ->
    url = Model.prototype.url.call @
    url += "?authenticate=false" if @isNew()
    url

module.exports = User
