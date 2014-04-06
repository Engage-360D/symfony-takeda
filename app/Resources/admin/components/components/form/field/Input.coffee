`/** @jsx React.DOM */`

React = require "React"
require "jquery.ui.autocomplete"

Input = React.createClass
  getDefaultProps: ->
    type: "text"
    autocomplete: null

  componentDidMount: ->
    if @props.autocomplete
      ac = $(@refs.input.getDOMNode())
        .autocomplete(@props.autocomplete)
      ac.autocomplete("widget")
        .addClass("InputAutocomplete")
      if @props.autocomplete.render
        ac.data("ui-autocomplete")._renderItem = @props.autocomplete.render

  render: ->
    `(
      <span className="Input">
        {this.transferPropsTo(
          <input className="InputElement" ref="input"/>
        )}
      </span>
    )`

module.exports = Input
