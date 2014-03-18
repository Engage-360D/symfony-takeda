`/** @jsx React.DOM */`

React = require "React"

Field = require "./../form/Field"
Input = require "./../form/field/Input"
Button = require "./../button/Button"

Auth = require "./../../services/Auth"

Login = React.createClass
  getInitialState: ->
    username: null
    password: null

  createChangeHandler: (field) ->
    (event) =>
      state = @state
      state[field] = event.target.value
      @setState state

  submit: ->
    Auth.login @state.username, @state.password

  render: ->
    `(
      <div className="Login">
        <Field>
          <Input
            placeholder="Логин"
            onChange={this.createChangeHandler("username")}/>
        </Field>
        <Field>
          <Input
            type="password"
            placeholder="Пароль"
            onChange={this.createChangeHandler("password")}/>
        </Field>
        <Button onClick={this.submit} mods={["Block", "Success"]}>Войти</Button>
      </div>
    )`

module.exports = Login
