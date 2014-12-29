reqwest = require "reqwest"

LoginMixin =
  login: (username, password, callback) ->
    reqwest
      url: "/api/v1/tokens"
      method: "POST"
      data:
        tokens:
          email: username
          plainPassword: password
      error: (error) =>
        callback JSON.parse error.response
      success: (response) =>
        callback()


module.exports = LoginMixin
