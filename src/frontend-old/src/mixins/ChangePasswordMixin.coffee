reqwest = require "reqwest"


ChangePasswordMixin =
  change: (password, confirm, callback) ->
    reqwest
      url: "/api/users/reset/#{@props.token}"
      type: "json"
      method: "POST"
      contentType: "application/json"
      data:  JSON.stringify plainPassword:
        first: @state.password
        second: @state.confirm
      error: (error) =>
        callback error
      success: (response) =>
        callback()


module.exports = ChangePasswordMixin
