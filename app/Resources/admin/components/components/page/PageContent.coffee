`/** @jsx React.DOM */`

React = require "react"

PageContent = React.createClass
  render: ->
    `(
      <div className="PageContent">
        {this.props.children}
      </div>
    )`

module.exports = PageContent
