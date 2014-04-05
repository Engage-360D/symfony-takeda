`/** @jsx React.DOM */`

React = require "react"


VkontakteButton = React.createClass
  onClick: ->
    window.open("/connect/vkontakte", "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a onClick={this.onClick} className="socail__vk" href="#">
        <i></i>
      </a>
    )`


module.exports = VkontakteButton
