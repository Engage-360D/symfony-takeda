`/** @jsx React.DOM */`

React = require "React"

PageContent = React.createClass
  render: ->
    `(
      <div className="PageContent">
        {this.props.children}
      </div>
    )`

module.exports = PageContent
