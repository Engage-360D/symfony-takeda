`/** @jsx React.DOM */`

React = require "React"

Input = React.createClass
  render: ->
    @transferPropsTo(
      `(
        <div className="Field">
          {this.props.children}
        </div>
      )`
    )

module.exports = Input
