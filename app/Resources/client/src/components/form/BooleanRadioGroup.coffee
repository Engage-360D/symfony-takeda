`/** @jsx React.DOM */`

React = require "react"
RadioGroup = require "./RadioGroup"


BooleanRadioGroup = React.createClass
  statics:
    values: [
      {value: "yes", text: "Да"}
      {value: "no", text: "Нет"}
    ]

  render: ->
    if typeof @props.valueLink.value is "boolean"
      value = if @props.valueLink.value then "yes" else "no"
    else
      value = ""
    
    link =
      value: value
      requestChange: (value) =>
        @props.valueLink.requestChange value is "yes"

    @transferPropsTo `(
      <RadioGroup values={BooleanRadioGroup.values} valueLink={link} />
    )`


module.exports = BooleanRadioGroup
