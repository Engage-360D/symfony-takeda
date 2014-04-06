`/** @jsx React.DOM */`

React = require "react"


FacebookButton = React.createClass
  onClick: ->
    window.open("/connect/facebook", "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a onClick={this.onClick} className="socail__fb" href="#">
        <i></i>
      </a>
    )`


module.exports = FacebookButton
