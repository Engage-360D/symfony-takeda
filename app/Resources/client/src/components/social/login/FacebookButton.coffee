`/** @jsx React.DOM */`

React = require "react"
reqwest = require "reqwest"
EventsMixin = require "../../../mixins/EventsMixin"

FacebookButton = React.createClass
  mixins: [EventsMixin]

  getDefaultProps: ->
    reloadOnSuccess: false

  componentDidMount: ->
    @addEventListener window, "loadSuccess", =>
      return if location.href.indexOf("/test") isnt -1
      window.location.reload()

  onClick: ->
    url = "/connect/facebook?_target_path=/account/modal_success"
    window.open(url, "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a onClick={this.onClick} className="socail__fb" href="#">
        <i></i>
      </a>
    )`


module.exports = FacebookButton
