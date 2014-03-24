$ = require "jquery"
require "jStorage"
require "JSOjs"

class Auth
  constructor: ->
    clientId = $.jStorage.get "clientId"
    @configure clientId if clientId

  configure: (clientId) ->
    $.jStorage.set "clientId", clientId
    $.jStorage.setTTL "clientId", 3600

    jso_configure
      engage360d: 
        client_id: clientId
        redirect_uri: location.pathname
        authorization: "/oauth/v2/auth"
        presenttoken: "header"

    unless jso_getToken "engage360d"
      jso_ensureTokens engage360d: []

  login: (username, password) ->
    $.post("/user/check", {_username: username, _password: password, _target_path: "/user/admin_success"})
      .success (response) =>
        @configure response.client_id

  logout: ->
    $.jStorage.set "clientId", null
    jso_wipe()
    window.location.href = "/admin/logout"

  isAuthorized: ->
    jso_getToken("engage360d")?

module.exports = new Auth()
