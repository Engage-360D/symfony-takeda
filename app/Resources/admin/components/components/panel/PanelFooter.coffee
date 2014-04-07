`/** @jsx React.DOM */`

React = require "react"

PanelFooter = React.createClass
  render: ->
    @transferPropsTo(
      `(
        <div className="PanelFooter">
          {this.props.children}
        </div>
      )`
    )

module.exports = PanelFooter
