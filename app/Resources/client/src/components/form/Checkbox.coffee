`/** @jsx React.DOM */`

React = require "react"


Checkbox = React.createClass
  render: ->
    @transferPropsTo `(
      <input className="Checkbox" type="checkbox" />
    )`


module.exports = Checkbox
