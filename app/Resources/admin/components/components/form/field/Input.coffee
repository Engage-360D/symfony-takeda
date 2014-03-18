`/** @jsx React.DOM */`

React = require "React"

Input = React.createClass
  getDefaultProps: ->
    type: "text"

  render: ->
    `(
      <span className="Input">
        {this.transferPropsTo(
          <input className="InputElement"/>
        )}
      </span>
    )`

module.exports = Input
