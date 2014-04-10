`/** @jsx React.DOM */`

React = require "react"


OdnoklassnikiButton = React.createClass
  getDefaultProps: ->
    reloadOnSuccess: false
    connected: false

  getInitialState: ->
    connected: if typeof @props.connected is "boolean" then @props.connected else @props.connected is "true"

  onClick: ->
    return if @state.connected
    #window.open("/connect/odnoklassniki", "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a
        href="#"
        onClick={this.onClick}
        className={this.props.connected ? "socail__ok socail__connected" : "socail__ok"}>
        <i></i>
      </a>
    )`


module.exports = OdnoklassnikiButton
