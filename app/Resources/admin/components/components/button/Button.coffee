`/** @jsx React.DOM */`

React = require "React"
ModsMixin = require "./../../mixins/ModsMixin"

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
        <button onClick={this.onClick} className={this.getClassName("Button")}>{this.props.children}</button>
      )`
    )

module.exports = Button
