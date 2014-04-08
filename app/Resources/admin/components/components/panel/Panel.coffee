`/** @jsx React.DOM */`

React = require "react"

Panel = React.createClass
  render: ->
    @transferPropsTo(
      `(
        <div className="Panel">
          {this.props.children}
        </div>
      )`
    )

module.exports = Panel
