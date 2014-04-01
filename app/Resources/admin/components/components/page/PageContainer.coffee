`/** @jsx React.DOM */`

React = require "React"

PageContainer = React.createClass
  render: ->
    `(
      <div className="PageContainer">
        {this.props.children}
      </div>
    )`

module.exports = PageContainer
