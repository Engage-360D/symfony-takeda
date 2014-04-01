`/** @jsx React.DOM */`

React = require "React"
require "jquery.ui.autocomplete"

Input = React.createClass
  getDefaultProps: ->
    type: "text"
    autocomplete: null

  componentDidMount: ->
    if @props.autocomplete
      $(@refs.input.getDOMNode())
        .autocomplete(@props.autocomplete)
        .autocomplete("widget")
        .addClass("InputAutocomplete")

  render: ->
    `(
      <span className="Input">
        {this.transferPropsTo(
          <input className="InputElement" ref="input"/>
        )}
      </span>
    )`

module.exports = Input
