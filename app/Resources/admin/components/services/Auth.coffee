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

  login: (username, password, callback) ->
    $.ajax
      url: "/account/check"
      method: "POST"
      data:
        _username: username
        _password: password
        _target_path: "/account/admin_success"
      success: (response) -> jso_ensureTokens engage360d: []
      error: (error) ->
        try
          error = JSON.parse error.responseText
        catch e
          error = {}
        callback error if typeof callback is 'function'

  logout: ->
    jso_wipe()
    window.location.href = "/account/logout?_target_path=/admin.html"

  isAuthorized: ->
    jso_getToken("engage360d")?

module.exports = Auth
