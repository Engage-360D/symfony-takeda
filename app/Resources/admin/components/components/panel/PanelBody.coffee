`/** @jsx React.DOM */`

React = require "react"

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
