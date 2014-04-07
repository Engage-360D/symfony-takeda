`/** @jsx React.DOM */`

React = require "react"


WindowLoaded = React.createClass
  componentDidMount: ->
    return unless window.opener
    event = new Event "loadSuccess"
    window.opener.dispatchEvent event
    window.close()

  render: ->
    `(
      <div/>
    )`


module.exports = WindowLoaded
