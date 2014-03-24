`/** @jsx React.DOM */`

React = require "React"

Label = React.createClass
  render: ->
    @transferPropsTo(
      `(
        <label ref="label" className="Label">
          {this.props.children}
        </label>
      )`
    )

module.exports = Label
