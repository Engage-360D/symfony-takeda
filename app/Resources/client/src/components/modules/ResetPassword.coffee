`/** @jsx React.DOM */`

React = require "react"
ResetPasswordMixin = require "../../mixins/ResetPasswordMixin"
HTMLElementContainerMixin = require "../../mixins/HTMLElementContainerMixin"
Modal = require "../modal/Modal"
Field = require "../registration/Field"
Input = require "../registration/Input"


ResetPassword = React.createClass
  mixins: [ResetPasswordMixin, HTMLElementContainerMixin]

  getInitialState: ->
    username: null

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

  onChange: (event) ->
    @setState username: event.target.value

  onSubmit: ->
    @reset @state.username, (response) ->
      console.log response

  renderModalBody: ->
    `(
      <div className="enter">
        <Field className="field_mod field_mod_middle">
          <div>Введите email для восстановлени пароля</div>
        </Field>
        <Field className="field_mod">
          <Input onChange={this.onChange}/>
        </Field>
        <div>
				  <button className="btn" onClick={this.onSubmit}>Прислать ссылку для восстановления</button>
				</div>
      </div>
    )`

  render: ->
    `(
      <a className="link link_black" href="#" onClick={this.onShow}>
        <span>Забыли пароль</span>
        <i></i>
      </a>
    )`


module.exports = ResetPassword
