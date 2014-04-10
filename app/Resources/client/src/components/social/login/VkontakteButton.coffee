`/** @jsx React.DOM */`

React = require "react"


VkontakteButton = React.createClass
  getDefaultProps: ->
    reloadOnSuccess: false
    connected: false

  onClick: ->
    return if @props.connected
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
