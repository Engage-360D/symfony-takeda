`/** @jsx React.DOM */`

React = require "react"

Page = React.createClass
  render: ->
    `(
      <div className="Page">
        {this.props.children}
      </div>
    )`

module.exports = Page
