`/** @jsx React.DOM */`

React = require "React"

Navigation = React.createClass
  render: ->
    `(
      <div className="Navigation">
        <div className="NavigationTitle">
          {this.props.title}
        </div>
        {this.props.children}
      </div>
    )`

module.exports = Navigation
