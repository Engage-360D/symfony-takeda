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

  getDefaultProps: ->
    reloadOnSuccess: true

  getInitialState: ->
    showValidation: false

  getValidationConfig: ->
    children:
      username:
        notNull: validationConstraints.notNull()
      password:
        notNull: validationConstraints.notNull()
    component:
      form: (state, childrenValidity) ->
        childrenValidity.username.valid and childrenValidity.password.valid

  onLogin: ->
    if @validity.component.form.invalid
      return @setState showDoctorValidation: true
    @login @state.username, @state.password, (err) =>
      @props.valueLink.requestChange true if @props.valueLink
      window.location.reload() if @props.reloadOnSuccess

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
      			    invalid={this.state.showValidation && this.validity.children.username.invalid}/>
      			</Field>
      			<Field>
      			  <Input
      			    type="password"
      			    placeholder="Пароль"
      			    valueLink={this.linkState('password')}
      			    invalid={this.state.showValidation && this.validity.children.password.invalid}/>
      			</Field>
      			<div className="enter__link">
  							<ResetPassword/>
  					</div>
  					<div className="enter__btn">
  							<button className="btn" onClick={this.onLogin}>Войти</button>
  					</div>
    			</div>
    			<div className="data__row data__row_social">
  					<div className="data__label">Использовать аккаунт социальных сетей:</div>
  					<ul className="social social_gray">
  						<li><VkontakteButton/></li>
  						<li><FacebookButton/></li>
  						<li><OdnoklassnikiButton/></li>
  					</ul>
  				</div>
        </div>
			</div>
    )`


module.exports = Login
