`/** @jsx React.DOM */`

React = require "react"

PageContainer = React.createClass
  render: ->
    `(
      <div className="PageContainer">
        {this.props.children}
      </div>
    )`

module.exports = PageContainer
