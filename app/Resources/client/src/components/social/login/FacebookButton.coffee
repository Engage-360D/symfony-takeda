`/** @jsx React.DOM */`

React = require "react"
reqwest = require "reqwest"

FacebookButton = React.createClass
  getDefaultProps: ->
    reloadOnSuccess: false

  componentDidMount: ->
    return unless @props.reloadOnSuccess
    window.addEventListener "loadSuccess", =>
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
