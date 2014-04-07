`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"
Field = require "Engage360d/components/form/Field"
Input = require "Engage360d/components/form/field/Input"
Button = require "Engage360d/components/button/Button"

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
    Ctx.get("auth").login @state.username, @state.password

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
