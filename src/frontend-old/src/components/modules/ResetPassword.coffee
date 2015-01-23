`/** @jsx React.DOM */`

React = require "react"
ResetPasswordMixin = require "../../mixins/ResetPasswordMixin"
HTMLElementContainerMixin = require "../../mixins/HTMLElementContainerMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
validationConstraints = require "../../services/validationConstraints"
Modal = require "../modal/Modal"
Field = require "../registration/Field"
Input = require "../registration/Input"


ResetPasswordBody = React.createClass
  mixins: [
    ResetPasswordMixin
    ValidationMixin
    LinkedStateMixin
  ]

  statics:
    error: "Неправильный email"

  getInitialState: ->
    invalidMessage: ResetPasswordBody.error
    showValidation: false
    invalid: false
    email: null
    resetted: false

  getValidationConfig: ->
    children:
      email:
        email: validationConstraints.email()
        notNull: validationConstraints.notNull()

  handleChange: (event) ->
    @setState email: event.target.value

  handleSubmit: ->
    @setState showValidation: true
    return if @validity.children.email.invalid
    @reset @state.email, (errors) =>
      if errors
        message = ""
        for key, error of errors
          message += error
        @setState
          invalidMessage: message
          invalid: true
      else
        @setState resetted: true

  handleClose: ->
    @props.onClose() if @props.onClose

  isInvalid: ->
    if @state.showValidation
      return @validity.children.email.invalid or @state.invalid
    false

  renderSuccess: ->
    `(
      <div className="enter">
        <Field className="field_mod field_mod_middle">
          <div>Дальнейшие инструкции отправлены на {this.state.email}.</div>
        </Field>
        <div>
				  <button className="btn" onClick={this.handleClose}>Закрыть</button>
				</div>
      </div>
    )`

  renderReset: ->
    `(
      <div className="enter">
        <Field className="field_mod field_mod_middle">
          <div>Введите email для восстановлени пароля</div>
        </Field>
        <Field className="field_mod">
          <Input
            valueLink={this.linkState("email")}
            invalidMessage={this.state.invalidMessage}
            invalid={this.isInvalid()}/>
        </Field>
        <div>
				  <button className="btn" onClick={this.handleSubmit}>Прислать ссылку для восстановления</button>
				</div>
      </div>
    )`

  render: ->
    if @state.resetted
      @renderSuccess()
    else
      @renderReset()


ResetPassword = React.createClass
  mixins: [
    ResetPasswordMixin
    HTMLElementContainerMixin
    LinkedStateMixin
  ]

  createModal: ->
    props =
      onClose: @onClose
      title: "Восстановление пароля"
      children: @renderModalBody()
    @modal = React.renderComponent Modal(props), @createContainer()

  onShow: (event) ->
    event.preventDefault()
    if @modal
      @modal.setState show: true
    else
      @createModal() 

  onClose: ->
    @modal.setState show: false

  renderModalBody: ->
    `(<ResetPasswordBody onClose={this.onClose}/>)`

  render: ->
    `(
      <a className="link link_black" href="#" onClick={this.onShow}>
        <span>Забыли пароль</span>
        <i></i>
      </a>
    )`


module.exports = ResetPassword
