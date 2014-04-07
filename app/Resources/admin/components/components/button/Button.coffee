`/** @jsx React.DOM */`

React = require "react"
ModsMixin = require "Engage360d/mixins/ModsMixin"

Button = React.createClass
  mixins: [ModsMixin]

  getDefaultProps: ->
    disabled: false

  onClick: (event) ->
    if @props.onClick and not @props.disabled
      @props.onClick event

  render: ->
    @transferPropsTo(
      `(
        <button disabled={this.props.disabled} onClick={this.onClick} className={this.getClassName("Button")}>{this.props.children}</button>
      )`
    )

module.exports = Button
