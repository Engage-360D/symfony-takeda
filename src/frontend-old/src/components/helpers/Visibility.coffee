`/** @jsx React.DOM */`

React = require "react"


Visibility = React.createClass
  getDefaultProps: ->
    show: null
    hide: null
    inline: false

  isVisible: ->
    if @props.show isnt null
      Boolean @props.show
    else if @props.hide isnt null
      not Boolean @props.hide
    else
      true

  render: ->
    style = {}

    if @isVisible()
      if @props.inline
        style.display = 'inline'
      else
        style.display = 'block'
    else
      style.display = 'none'

    @transferPropsTo `(
      <div style={style}>{this.props.children}</div>
    )`


module.exports = Visibility
