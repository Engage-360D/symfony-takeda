`/** @jsx React.DOM */`

React = require "react"


FacebookButton = React.createClass
  statics:
    FB_APP_ID: 245182945513174

  componentWillMount: ->
    script = document.createElement "script"
    script.setAttribute "src", "//connect.facebook.net/ru_RU/all.js"
    script.onload = @onLoad
    document.head.appendChild script

  onLoad: ->
    FB.init
      appId: FacebookButton.FB_APP_ID
      status: true

  onClick: ->
    FB.getLoginStatus (response) ->
      if response.status is "connected"
        window.open("/connect/facebook", "", "width=800,height=650")
      else
        FB.login (response) ->
          window.open("/connect/facebook", "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a onClick={this.onClick} className="socail__fb" href="#">
        <i></i>
      </a>
    )`


module.exports = FacebookButton
