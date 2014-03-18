`/** @jsx React.DOM */`

React = require "React"

Icon = React.createClass
  getDefaultProps: ->
    name: null

  render: ->
    `(
      <i className={"fa fa-" + this.props.name}/>
    )`

module.exports = Icon
