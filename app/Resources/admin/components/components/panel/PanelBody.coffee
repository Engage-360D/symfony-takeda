`/** @jsx React.DOM */`

React = require "React"

PanelBody = React.createClass
  render: ->
    @transferPropsTo(
      `(
        <div className="PanelBody">
          {this.props.children}
        </div>
      )`
    )

module.exports = PanelBody
