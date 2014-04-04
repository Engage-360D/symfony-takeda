`/** @jsx React.DOM */`

React = require "react"


Input = React.createClass
  getDefaultProps: ->
    type: "text"
    invalid: false

  renderInput: ->
    @transferPropsTo (
      `(
        <input type={this.props.type}/>
      )`
    )

  render: ->
    classes = ["field__in"]
    classes.push "field__in_invalid" if @props.invalid
    `(
      <div className={classes.join(" ")}>
    		{this.renderInput()}
    	</div>
    )`


module.exports = Input
