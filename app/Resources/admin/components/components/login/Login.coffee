`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"
Field = require "Engage360d/components/form/Field"
Input = require "Engage360d/components/form/field/Input"
Button = require "Engage360d/components/button/Button"
Modal = require "Engage360d/components/modal/Modal"

ValidationMixin = require "Engage360d/mixins/ValidationMixin"
HTMLElementContainerMixin = require "Engage360d/mixins/HTMLElementContainerMixin"
ErrorMessageMixin = require "Engage360d/mixins/ErrorMessageMixin"
validationConstraints = require "Engage360d/services/ValidationConstraints"

Login = React.createClass
  mixins: [
    ValidationMixin
    HTMLElementContainerMixin
    ErrorMessageMixin
  ]

  getInitialState: ->
    username: null
    password: null
    usernameInvalid: false
    passwordInvalid: false
    usernameInvalidMessage: null
    passwordInvalidMessage: null

  createChangeHandler: (field) ->
    (event) =>
      state = @state
      state[field] = event.target.value
      @setState state

  getValidationConfig: ->
    children:
      username:
        minLength: validationConstraints.minLength 3
        email: validationConstraints.email()
      password:
        minLength: validationConstraints.minLength 3
    component:
      form: (state, childrenValidity) ->
        childrenValidity.username.valid and childrenValidity.password.valid

  isUsernameInvalid: ->
    return false unless @state.showValidation
    @validity.children.username.invalid or @state.usernameInvalid

  isPasswordInvalid: ->
    return false unless @state.showValidation
    @validity.children.password.invalid or @state.passwordInvalid

  getUsernameInvalidMessage: ->
    getUsernameInvalidMessage: ->
    return @state.usernameInvalidMessage if @state.usernameInvalidMessage
    if @state.username and @state.username.length > 0
      @getErrorMessage('email')
    else
      @getErrorMessage('blank')

  showLoginFailureMessage: ->
    modal = null
    props =
      onClose: =>
        modal.setState show: false
      title: "Ошибка"
      children: @getErrorMessage('unknown')
    modal = React.renderComponent Modal(props), @createContainer()

  submit: ->
    @setState
      showValidation: true
      usernameInvalid: false
      passwordInvalid: false

    return if @validity.component.form.invalid
    Ctx.get("auth").login @state.username, @state.password, (error) =>
      if error
        if error.username
          @setState usernameInvalidMessage: error.username, usernameInvalid: true
        else if error.password
          @setState passwordInvalidMessage: error.password, passwordInvalid: true
        else
          @showLoginFailureMessage()

  render: ->
    `(
      <div className="Login">
        <Field>
          <Input
            placeholder="Логин"
            onChange={this.createChangeHandler("username")}
            invalid={this.isUsernameInvalid()}
            invalidMessage={this.getUsernameInvalidMessage()}/>
        </Field>
        <Field>
          <Input
            type="password"
            placeholder="Пароль"
            onChange={this.createChangeHandler("password")}
            invalid={this.isPasswordInvalid()}
            invalidMessage={this.state.passwordInvalidMessage || this.getErrorMessage('minLength')}/>
        </Field>
        <Button onClick={this.submit} mods={["Block", "Success"]}>Войти</Button>
      </div>
    )`

module.exports = Login
