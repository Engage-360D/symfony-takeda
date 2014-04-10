reqwest = require "reqwest"

ResetPasswordMixin =
  reset: (username, callback) ->
    data = JSON.stringify username: username
    reqwest
      url: "/api/users/resets"
      type: "json"
      method: "POST"
      contentType: "application/json"
      data:  data
      error: (error) =>
        callback JSON.parse error.response
      success: (response) =>
        callback()


module.exports = ResetPasswordMixin
