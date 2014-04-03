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
    link =
      value: if @props.valueLink.value then "yes" else "no"
      requestChange: (value) =>
        @props.valueLink.requestChange value is "yes"

    @transferPropsTo `(
      <RadioGroup values={BooleanRadioGroup.values} valueLink={link} />
    )`


module.exports = BooleanRadioGroup
