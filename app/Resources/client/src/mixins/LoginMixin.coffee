reqwest = require "reqwest"

LoginMixin =
  login: (username, password, callback) ->
    reqwest
      url: "/account/check"
      method: "POST"
      data:
        _username: username
        _password: password
      error: (err) =>
        callback err
      success: (response) =>
        callback()


module.exports = LoginMixin
