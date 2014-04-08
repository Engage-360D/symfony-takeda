`/** @jsx React.DOM */`

React = require "react"


OdnoklassnikiButton = React.createClass
  onClick: ->
    #window.open("/connect/odnoklassniki", "", "width=800,height=650")

  render: ->
    @transferPropsTo `(
      <a onClick={this.onClick} className="socail__ok" href="#">
        <i></i>
      </a>
    )`


module.exports = OdnoklassnikiButton
