`/** @jsx React.DOM */`

React = require "react"

Logo = React.createClass
  render: ->
    @transferPropsTo(
      `(
        <div className="Logo">
          <div>
            <img src="http://iengage.ru/images/logo.png"/>
          </div>
        </div>
      )`
    )

module.exports = Logo
