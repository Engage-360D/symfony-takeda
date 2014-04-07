`/** @jsx React.DOM */`

React = require "react"

Container = React.createClass
  getDefaultProps: ->
    padding: 20
    inline: false

  render: ->
    styles = @props.style or {}
    styles.padding = @props.padding
    styles.display = "inline-block" if @props.inline

    @transferPropsTo(
      `(
        <div className="Container" style={styles}>
          {this.props.children}
        </div>
      )`
    )

module.exports = Container
