`/** @jsx React.DOM */`

React = require "react"
require "jquery.ui.autocomplete"

ValidationError = require "Engage360d/components/form/ValidationError"

Input = React.createClass
  getDefaultProps: ->
    type: "text"
    autocomplete: null
    invalid: false
    invalidMessage: null

  componentDidMount: ->
    if @props.autocomplete
      ac = $(@refs.input.getDOMNode())
        .autocomplete(@props.autocomplete)
      ac.autocomplete("widget")
        .addClass("InputAutocomplete")
      if @props.autocomplete.render
        ac.data("ui-autocomplete")._renderItem = @props.autocomplete.render

  renderInput: ->
    return @transferPropsTo(
      `(
        <input className="InputElement" ref="input"/>
      )`
    )

  renderValidation: ->
    return unless @props.invalid
    `(
      <ValidationError message={this.props.invalidMessage} />
    )`

  render: ->
    `(
      <span className="Input">
        {this.renderInput()}
        {this.renderValidation()}
      </span>
    )`

module.exports = Input
