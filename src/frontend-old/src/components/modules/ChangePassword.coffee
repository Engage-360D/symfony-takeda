`/** @jsx React.DOM */`

React = require "react"
ResetPasswordMixin = require "../../mixins/ResetPasswordMixin"
HTMLElementContainerMixin = require "../../mixins/HTMLElementContainerMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
ChangePasswordMixin = require "../../mixins/ChangePasswordMixin"
validationConstraints = require "../../services/validationConstraints"
Modal = require "../modal/Modal"
Field = require "../registration/Field"
Input = require "../registration/Input"


ChangePassword = React.createClass
  mixins: [
    HTMLElementContainerMixin
    ValidationMixin
    ChangePasswordMixin
    LinkedStateMixin
  ]

  statics:
    errors:
      minLength: "Минимальная длина 3 символа"
      coincide: "Пароль не совпадает"

  getDefaultProps: ->
    token: null

  getInitialState: ->
    passwordInvalidMessage: null
    confirmInvalidMessage: null
    showValidation: false
    invalid: false

  getValidationConfig: ->
    children:
      password:
        minLength: validationConstraints.minLength 3
      confirm:
        minLength: validationConstraints.minLength 3
        coincide: (value) => value is @state.password
    component:
      form: (state, childrenValidity) ->
        childrenValidity.password.valid and childrenValidity.confirm.valid

  handleSubmit: ->
    @setState showValidation: true
    return if @validity.component.form.invalid
    @change @state.password, @state.confirm, (error) =>
      return if error
      @createModal()

  getConfirmInvalidMessage: ->
    if @state.confirm and @state.confirm.length > 3
      ChangePassword.errors.coincide
    else
      ChangePassword.errors.minLength

  createModal: ->
    props =
      onClose: @handleClose
      title: "Восстановление пароля"
      children: @renderModalBody()
    modal = React.renderComponent Modal(props), @createContainer()
    modal.setState show: true

  handleClose: ->
    window.location.pathname = "/account"

  renderModalBody: ->
    `(
      <div className="enter">
        <Field className="field_mod field_mod_middle">
          <div>Ваш пароль успешно изменен.</div>
        </Field>
        <div>
				  <button className="btn" onClick={this.handleClose}>Закрыть</button>
				</div>
      </div>  
    )`

  render: ->
    `(
      <div className="data ChangePassword">
				<div>
          <div className="data__title">
            Сменить пароль
          </div>
  				<div className="enter">
      			<Field className="field_mod">
      			  <Input
      			    type="password"
      			    placeholder="Пароль"
      			    valueLink={this.linkState('password')}
      			    invalid={this.state.showValidation && this.validity.children.password.invalid}
      			    invalidMessage={this.state.passwordInvalidMessage || ChangePassword.errors.minLength}/>
      			</Field>
      			<Field>
      			  <Input
      			    type="password"
      			    placeholder="Подтверждение"
      			    valueLink={this.linkState('confirm')}
      			    invalid={this.state.showValidation && this.validity.children.confirm.invalid}
      			    invalidMessage={this.getConfirmInvalidMessage()}/>
      			</Field>
  					<div className="enter__btn">
  							<button className="btn" onClick={this.handleSubmit}>Изменить</button>
  					</div>
    			</div>
        </div>
			</div>
    )`


module.exports = ChangePassword
