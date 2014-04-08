`/** @jsx React.DOM */`

React = require "react"

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
