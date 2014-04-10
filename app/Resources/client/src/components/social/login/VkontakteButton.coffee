`/** @jsx React.DOM */`

React = require "react"


VkontakteButton = React.createClass
  getDefaultProps: ->
    reloadOnSuccess: false
    connected: false

  getInitialState: ->
    connected: if typeof @props.connected is "boolean" then @props.connected else @props.connected is "true"

  onClick: ->
    return if @state.connected
    url = "/connect/vkontakte?_target_path=/account/modal_success"
    window.open(url, "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a
        href="#"
        onClick={this.onClick}
        className={this.props.connected ? "socail__vk socail__connected" : "socail__vk"}>
        <i></i>
      </a>
    )`


module.exports = VkontakteButton
