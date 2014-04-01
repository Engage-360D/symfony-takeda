`/** @jsx React.DOM */`

React = require "React"

PageHeader = React.createClass
  render: ->
    `(
      <div className="PageHeader">
        <h2>
          {this.props.children}
        </h2>
      </div>
    )`

module.exports = PageHeader
