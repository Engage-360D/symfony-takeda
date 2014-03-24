`/** @jsx React.DOM */`

React = require "react"


Label = React.createClass
  render: ->
    @transferPropsTo `(
      <label className="Label">
        {this.props.children}
      </label>
    )`


module.exports = Label
