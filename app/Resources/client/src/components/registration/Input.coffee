`/** @jsx React.DOM */`

React = require "react"
require "jquery-placeholder"


ValidationError = require "./ValidationError"

Input = React.createClass
  getDefaultProps: ->
    type: "text"
    invalid: false
    invalidMessage: null

  componentDidMount: ->
    $(@refs.input.getDOMNode()).placeholder()

  renderInput: ->
    @transferPropsTo (
      `(
        <input type={this.props.type} ref="input"/>
      )`
    )

  renderValidation: ->
    return unless @props.invalid
    `(
      <ValidationError message={this.props.invalidMessage}/>
    )`

  render: ->
    classes = ["field__in"]
    classes.push "field__in_invalid" if @props.invalid
    `(
      <div className={classes.join(" ")}>
    		{this.renderInput()}
    		{this.renderValidation()}
    	</div>
    )`


module.exports = Input
