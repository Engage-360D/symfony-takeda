`/** @jsx React.DOM */`

React = require "React"

Tab = React.createClass
  getDefaultProps: ->
    title: null

  getInitialState: ->
    active: false

  render: ->
    `(
      <div>
        {this.props.children}
      </div>
    )`

module.exports = Tab
