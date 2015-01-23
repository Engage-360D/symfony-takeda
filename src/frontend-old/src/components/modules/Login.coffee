`/** @jsx React.DOM */`

React = require "react"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
RegistrationMixin = require "../../mixins/RegistrationMixin"
HTMLElementContainerMixin = require "../../mixins/HTMLElementContainerMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
LoginMixin = require "../../mixins/LoginMixin"
validationConstraints = require "../../services/validationConstraints"


Modal = require "../modal/Modal"
Checkbox = require "../form/Checkbox"
Field = require "../registration/Field"
Input = require "../registration/Input"
ResetPassword = require "./ResetPassword"
FacebookButton = require "../social/login/FacebookButton"
VkontakteButton = require "../social/login/VkontakteButton"
OdnoklassnikiButton = require "../social/login/OdnoklassnikiButton"


Login = React.createClass
  mixins: [
    LinkedStateMixin
    ValidationMixin
    HTMLElementContainerMixin
    LoginMixin
  ]

  statics:
    errors:
      minLength: "Минимальная длина 3 символа"
      email: "Некорректный email адресс"
      default: "При попытке авторизации произошла ошибка. Попробуйте позже."

  getDefaultProps: ->
    reloadOnSuccess: true

  getInitialState: ->
    showValidation: false
    usernameInvalidMessage: null
    passwordInvalidMessage: null
    usernameInvalid: false
    passwordInvalid: false

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

  handleLogin: ->
    @setState
      showValidation: true
      usernameInvalid: false
      passwordInvalid: false

    return if @validity.component.form.invalid
    @login @state.username, @state.password, (error) =>
      if error
        if error.username
          @setState usernameInvalidMessage: error.username, usernameInvalid: true
        else if error.password
          @setState passwordInvalidMessage: error.password, passwordInvalid: true
        else
          @showLoginFailureMessage()
      else
        @props.valueLink.requestChange true if @props.valueLink
        window.location.reload() if @props.reloadOnSuccess

  handleLoginSuccess: ->
    @props.valueLink.requestChange true if @props.valueLink

  getUsernameInvalidMessage: ->
    return @state.usernameInvalidMessage if @state.usernameInvalidMessage
    if @state.username and @state.username.length > 0
      Login.errors.email
    else
      Login.errors.minLength

  isUsernameInvalid: ->
    return false unless @state.showValidation
    @validity.children.username.invalid or @state.usernameInvalid

  isPasswordInvalid: ->
    return false unless @state.showValidation
    @validity.children.password.invalid or @state.passwordInvalid

  showLoginFailureMessage: ->
    modal = null
    props =
      onClose: =>
        modal.setState show: false
      title: "Ошибка"
      children: Login.errors.default
    modal = React.renderComponent Modal(props), @createContainer()

  render: ->
    `(
      <div className="data">
				<div>
          <div className="data__title">
            Личные данные
          </div>
  				<div className="enter">
      			<Field className="field_mod">
      			  <Input
      			    placeholder="Email"
      			    valueLink={this.linkState('username')}
      			    invalid={this.isUsernameInvalid()}
      			    invalidMessage={this.getUsernameInvalidMessage()}/>
      			</Field>
      			<Field>
      			  <Input
      			    type="password"
      			    placeholder="Пароль"
      			    valueLink={this.linkState('password')}
      			    invalid={this.isPasswordInvalid()}
      			    invalidMessage={this.state.passwordInvalidMessage || Login.errors.minLength}/>
      			</Field>
      			<div className="enter__link">
  							<ResetPassword/>
  					</div>
  					<div className="enter__btn">
  							<button className="btn" onClick={this.handleLogin}>Войти</button>
  					</div>
    			</div>
    			<div className="data__row data__row_social">
  					<div className="data__label">Использовать аккаунт социальных сетей:</div>
  					<ul className="social social_gray">
  						<li><VkontakteButton onLogin={this.handleLoginSuccess}/></li>
  						<li><FacebookButton onLogin={this.handleLoginSuccess}/></li>
  						<li><OdnoklassnikiButton onLogin={this.handleLoginSuccess}/></li>
  					</ul>
  				</div>
        </div>
			</div>
    )`


module.exports = Login
