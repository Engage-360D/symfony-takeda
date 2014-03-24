`/** @jsx React.DOM */`

React = require "React"

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
