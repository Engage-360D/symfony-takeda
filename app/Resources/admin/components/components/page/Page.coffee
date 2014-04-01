`/** @jsx React.DOM */`

React = require "React"

Page = React.createClass
  render: ->
    `(
      <div className="Page">
        {this.props.children}
      </div>
    )`

module.exports = Page
