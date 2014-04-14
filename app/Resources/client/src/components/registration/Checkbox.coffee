`/** @jsx React.DOM */`

React = require "react"
ModsMixin = require "../../mixins/ModsMixin"


Checkbox = React.createClass
  mixins: [ModsMixin]

  getDefaultProps: ->
    invalid: false

  renderInput: ->
    @transferPropsTo `(
      <input type="checkbox"/>
    )`

  render: ->
    `(
      <label className={this.props.invalid ? "checkbox checkbox_invalid" : "checkbox"}>
        {this.renderInput()}
        <span>
          {this.props.children}
        </span>
      </label>
    )`


module.exports = Checkbox
