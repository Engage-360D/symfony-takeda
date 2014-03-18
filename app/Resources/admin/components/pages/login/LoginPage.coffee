`/** @jsx React.DOM */`

React = require "React"

Login = require "./../../components/login/Login"

LoginPage = React.createClass
  render: ->
    `(
      <div className="LoginPage">
        <Login/>
      </div>
    )`

module.exports = LoginPage
