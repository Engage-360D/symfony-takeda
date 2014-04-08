`/** @jsx React.DOM */`

React = require "react"

PanelHeader = React.createClass
  getDefaultProps: ->
    title: null

  render: ->
    @transferPropsTo(
      `(
        <div className="PanelHeader">
          <h4>{this.props.title}</h4>
          <p>{this.props.children}</p>
        </div>
      )`
    )

module.exports = PanelHeader
