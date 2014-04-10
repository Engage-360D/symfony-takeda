`/** @jsx React.DOM */`

React = require "react"


WindowLoaded = React.createClass
  componentDidMount: ->
    return unless window.opener
    try
      window.opener.loadSuccess()
    catch error
      window.parent.loadSuccess()
    window.close()

  render: ->
    `(
      <div/>
    )`


module.exports = WindowLoaded
