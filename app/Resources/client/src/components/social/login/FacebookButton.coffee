`/** @jsx React.DOM */`

React = require "react"
reqwest = require "reqwest"
EventsMixin = require "../../../mixins/EventsMixin"

FacebookButton = React.createClass
  mixins: [EventsMixin]

  getDefaultProps: ->
    reloadOnSuccess: false
    connected: false

  componentDidMount: ->
    @addEventListener window, "loadSuccess", =>
      return if location.href.indexOf("/test") isnt -1
      window.location.reload()

  onClick: ->
    return if @props.connected
    url = "/connect/facebook?_target_path=/account/modal_success"
    window.open(url, "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a
        href="#"
        onClick={this.onClick}
        className={this.props.connected ? "socail__fb socail__connected" : "socail__fb"}>
        <i></i>
      </a>
    )`


module.exports = FacebookButton
