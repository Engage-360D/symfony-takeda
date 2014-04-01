$ = require "jquery"
require "jStorage"
require "JSOjs"

class Auth
  constructor: (clientId) ->
    @configure clientId if clientId

  configure: (clientId) ->
    jso_configure
      engage360d: 
        client_id: clientId
        redirect_uri: location.pathname
        authorization: "/oauth/v2/auth"
        presenttoken: "header"
        isDefault: true

  login: (username, password) ->
    $.post("/user/check", {_username: username, _password: password, _target_path: "/user/admin_success"})
      .success (response) =>
        jso_ensureTokens engage360d: []

  logout: ->
    jso_wipe()
    window.location.href = "/user/logout"

  isAuthorized: ->
    jso_getToken("engage360d")?

module.exports = Auth
