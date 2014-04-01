`/** @jsx React.DOM */`

React = require "React"

ModsMixin = require "Engage360d/mixins/ModsMixin"

Column = React.createClass
  mixins: [ModsMixin]

  render: ->
    @transferPropsTo(
      `(
        <div className={this.getClassName("Column")}>
          {this.props.children}
        </div>
      )`
    )

module.exports = Column
