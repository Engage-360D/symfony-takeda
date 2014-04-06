`/** @jsx React.DOM */`

React = require "react"
LinkedValueUtils = require "react/lib/LinkedValueUtils"


RadioGroup = React.createClass
  renderValue: (value) ->
    checked = LinkedValueUtils.getValue(@) is value.value
    onChange = (event) =>
      if @props.valueLink
        @props.valueLink.requestChange value.value
      else if @props.onChange
        @props.onChange event

    `(
      <label className="radio">
        <input type="radio" checked={checked} onChange={onChange} />
        <span>{value.text}</span>
      </label>
    )`

  render: ->
    style = {}
    
    if @props.invalid
      style.color = 'red'
    
    @transferPropsTo `(
      <span style={style}>
        {this.props.values.map(this.renderValue)}
      </span>
    )`


module.exports = RadioGroup
