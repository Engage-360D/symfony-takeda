`/** @jsx React.DOM */`

React = require "react"


OdnoklassnikiButton = React.createClass
  getDefaultProps: ->
    reloadOnSuccess: false
    connected: false

  onClick: ->
    return if @props.connected
    #window.open("/connect/odnoklassniki", "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a
        href="#"
        onClick={this.onClick}
        className={this.props.connected ? "socail__ok socail__connected" : "socail__fb"}>
        <i></i>
      </a>
    )`


module.exports = OdnoklassnikiButton
