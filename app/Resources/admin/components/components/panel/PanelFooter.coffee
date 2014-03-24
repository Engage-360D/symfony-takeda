`/** @jsx React.DOM */`

React = require "React"

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
