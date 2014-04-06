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
      error: (err) =>
        console.log err
      success: (response) =>
        console.log response


module.exports = ResetPasswordMixin
