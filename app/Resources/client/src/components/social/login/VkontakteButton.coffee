`/** @jsx React.DOM */`

React = require "react"


VkontakteButton = React.createClass
  onClick: ->
    url = "/connect/vkontakte?_target_path=/account/modal_success"
    window.open(url, "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a onClick={this.onClick} className="socail__vk" href="#">
        <i></i>
      </a>
    )`


module.exports = VkontakteButton
