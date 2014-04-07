`/** @jsx React.DOM */`

React = require "react"
$ = require "jquery"

Button = require "Engage360d/components/button/Button"

FacebookButton = React.createClass
  componentWillMount: ->
    config =
      FB_APP_ID: 245182945513174
    $.ajaxSetup cache: true
    $.getScript "//connect.facebook.net/en_UK/all.js", =>
      FB.init appId: config.FB_APP_ID, status: true
      FB.Event.subscribe "auth.authResponseChange", @register

  register: (response) ->
    data =
      access_token: response.authResponse.accessToken
    $.post "/admin/facebook_check", data, (response) =>
      console.log response
      return
      @setState key: response.oauth_consumer_key
      FB.api "/me", (user) =>
        @setState
          name: user.name
          image: "http://graph.facebook.com/#{user.id}/picture"

  render: ->
    `(
      <Button mods={["Block", "Success"]}>FB</Button>
    )`

module.exports = FacebookButton
