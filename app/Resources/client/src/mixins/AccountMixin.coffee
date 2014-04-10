reqwest = require "reqwest"
moment = require "moment"

fields = [
  "firstname", "lastname", "email", "region", "specialization",
  "experience", "address", "phone", "institution"
]

extractFields = (data) ->
  user = {}
  for field in fields
    user[field] = data[field] if data[field]
  if data.graduation
    user.graduation = data.graduation.format "YYYY-MM-DD"
  JSON.stringify user

AccountMixin =
  load: (callback) ->
    reqwest
      url: "/api/users/me"
      type: "json"
      contentType: "application/json"
      success: callback

  loadTestResults: (callback) ->
    reqwest
      url: "/api/test-results"
      type: "json"
      contentType: "application/json"
      success: callback

  save: (callback) ->
    reqwest
      url: "/api/users/#{@state.id}"
      type: "json"
      method: "PUT"
      contentType: "application/json"
      data:  extractFields @state
      error: (error) ->
        callback JSON.parse error.response
      success: ->
        callback()
    

module.exports = AccountMixin
