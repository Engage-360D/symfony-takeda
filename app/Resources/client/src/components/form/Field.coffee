`/** @jsx React.DOM */`

React = require "react"
ModsMixin = require "../../mixins/ModsMixin"


Field = React.createClass
  mixins: [ModsMixin]

  render: ->
    @transferPropsTo `(
      <div className={this.getClassName('Field', ['TwoLine'])}>
        {this.props.children}
      </div>
    )`


module.exports = Field
