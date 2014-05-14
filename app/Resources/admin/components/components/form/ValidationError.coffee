`/** @jsx React.DOM */`

React = require "react"


ValidationError = React.createClass
  getDefaultProps: ->
    message: "Ошибка заполнения формы."

  render: ->
    `(
    	<div className="ValidationError">
    	  {this.props.message}
    	</div>
    )`


module.exports = ValidationError
