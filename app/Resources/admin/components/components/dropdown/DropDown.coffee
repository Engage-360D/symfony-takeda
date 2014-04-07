`/** @jsx React.DOM */`

React = require "react"

ModsMixin = require "Engage360d/mixins/ModsMixin"

DropDown = React.createClass
  mixins: [ModsMixin]

  getDefaultProps: ->
    show: false

  render: ->
    return `(<span/>)` unless @props.show

    @transferPropsTo(
      `(
        <div className={this.getClassName("DropDown")}>
          {this.props.children}
        </div>
      )`
    )

module.exports = DropDown
