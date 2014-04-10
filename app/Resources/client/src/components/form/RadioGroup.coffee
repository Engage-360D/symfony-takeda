`/** @jsx React.DOM */`

React = require "react"
cx = require "react/lib/cx"
LinkedValueUtils = require "react/lib/LinkedValueUtils"


RadioGroup = React.createClass
  renderValue: (value, index) ->
    checked = LinkedValueUtils.getValue(@) is value.value
    onChange = (event) =>
      if @props.valueLink
        @props.valueLink.requestChange value.value
      else if @props.onChange
        @props.onChange event
        
    classes = cx
      "radio": true
      "is-error": @props.invalid

    `(
      <label className={classes} key={index}>
        <input type="radio" checked={checked} onChange={onChange} />
        <span>{value.text}</span>
      </label>
    )`

  render: ->
    @transferPropsTo `(
      <span>
        {this.props.values.map(this.renderValue)}
      </span>
    )`


module.exports = RadioGroup
