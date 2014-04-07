`/** @jsx React.DOM */`

React = require "react"
ModsMixin = require "../../mixins/ModsMixin"


Checkbox = React.createClass
  mixins: [ModsMixin]

  renderInput: ->
    @transferPropsTo `(
      <input type="checkbox"/>
    )`

  render: ->
    `(
      <label className="checkbox">
        {this.renderInput()}
        <span>
          {this.props.children}
        </span>
      </label>
    )`


module.exports = Checkbox
