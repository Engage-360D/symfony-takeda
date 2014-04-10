reqwest = require "reqwest"
moment = require "moment"

fields = [
  "firstname", "email", "region", "specialization",
  "experience", "address", "phone", "institution",
  "facebookId", "vkontakteId"
]

extractFields = (data) ->
  user = {}
  for field in fields
    user[field] = data[field] if data[field]
  user.plainPassword =
    first: data.password
    second: data.confirmPassword
  user.doctor = if data.doctor then true else false
  if data.graduation
    user.graduation = moment(data.graduation, "YYYY").format "YYYY-MM-DD"
  JSON.stringify user

RegistrationMixin =
  register: (callback) ->
    reqwest
      url: "/api/users"
      type: "json"
      method: "POST"
      contentType: "application/json"
      data:  extractFields @state
      error: (error) =>
        callback JSON.parse error.response
      success: =>
        callback()

  login: (username, password) ->
    reqwest
      url: "/user/check"
      type: "json"
      method: "POST"
      contentType: "application/json"
      data:  JSON.stringify _username: username, _password: password
      error: (err) =>
        #console.log err
      success: (response) =>
        #console.log response


module.exports = RegistrationMixin
