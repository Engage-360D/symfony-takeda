`/** @jsx React.DOM */`

React = require "react"
LinkedValueUtils = require "react/lib/LinkedValueUtils"

Switch = React.createClass
  onClick: ->
    @props.valueLink.requestChange not LinkedValueUtils.getValue @

  render: ->
    checked = LinkedValueUtils.getValue @

    `(
      <span className="Switch" onClick={this.onClick}>
        <small className={checked ? "SwitchButton SwitchButton-Checked" : "SwitchButton"}></small>
      </span>
    )`

module.exports = Switch
