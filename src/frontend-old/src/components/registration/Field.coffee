`/** @jsx React.DOM */`

React = require "react"
ModsMixin = require "../../mixins/ModsMixin"


Field = React.createClass
  mixins: [ModsMixin]

  render: ->
    @transferPropsTo `(
      <div className={this.getClassName('field')}>
        {this.props.children}
      </div>
    )`


module.exports = Field
