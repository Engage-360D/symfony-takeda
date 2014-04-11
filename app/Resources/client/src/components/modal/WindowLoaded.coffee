`/** @jsx React.DOM */`

React = require "react"


WindowLoaded = React.createClass
  componentDidMount: ->
    return unless window.opener
    try
      window.opener.loginSuccess()
    catch error
      window.parent.loginSuccess()
    window.close()

  render: ->
    `(
      <div/>
    )`


module.exports = WindowLoaded
