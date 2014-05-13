`/** @jsx React.DOM */`

React = require "react"
moment = require "moment"

AccountMixin = require "../../mixins/AccountMixin"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
HTMLElementContainerMixin = require "../../mixins/HTMLElementContainerMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
validationConstraints = require "../../services/validationConstraints"
Visibility = require "../helpers/Visibility"

Modal = require "../modal/Modal"
Field = require "../registration/Field"
Input = require "../registration/Input"
DateInput = require "../form/DateInput"
BooleanRadioGroup = require "../form/BooleanRadioGroup"
TestResultRecommendations = require "./TestResultRecommendations"

Account = React.createClass
  mixins: [
    AccountMixin
    LinkedStateMixin
    ValidationMixin
    HTMLElementContainerMixin
  ]

  statics:
    tabs:
      test: 0
      settings: 1
    errors:
      minLength: "Минимальная длина 3 символа"
      email: "Некорректный email адресс"

  getInitialState: ->
    showValidation: false
    active: Account.tabs.test
    errors: null
    testResults: null

  getValidationConfig: ->
    children:
      email:
        minLength: validationConstraints.minLength 3
        email: validationConstraints.email()
      firstname:
        minLength: validationConstraints.minLength 3
    component:
      account: (state, childrenValidity) ->
        childrenValidity.email.valid and childrenValidity.firstname.valid

  componentWillMount: ->
    @load (user) =>
      user.graduation = moment(user.graduation) if user.graduation
      @defaultUser = user
      @setState user
    @loadTestResults (results) =>
      @setState testResults: results

  createTabChangeHandler: (tab) ->
    => @setState active: tab

  handleLogout: ->
    window.location = "/account/logout"

  handleSave: ->
    @setState showValidation: true
    return if @validity.component.account.invalid
    @save (error) =>
      if error
        @setState errors: error
      else
        @createModal()

  handleCancel: ->
    @setState @defaultUser

  createModal: ->
    unless @modal
      props =
        onClose: @handleClose
        #title: "Изменение личных данных"
        children: @renderModalBody()
      @modal = React.renderComponent Modal(props), @createContainer()
    @modal.setState show: true

  handleClose: ->
    @modal.setState show: false

  isEmailInvalid: ->
    return false unless @state.showValidation
    @validity.children.email.invalid || (@state.errors and @state.errors.email)

  getEmailInvalidMessage: ->
    if @state.errors and @state.errors.email
      return @state.errors.email.join " "
    Account.errors.email

  openDoctorGraduationCalendar: ->
    @refs.doctorGraduationCalendar.open()

  renderModalBody: ->
    `(
      <div className="enter">
        <Field className="field_mod field_mod_middle">
          <div>Ваш аккаунт успешно изменен.</div>
        </Field>
        <div>
				  <button className="btn" onClick={this.handleClose}>OK</button>
				</div>
      </div>  
    )`

  renderTests: ->
    return unless @state.testResults
    
    @state.testResults.map (test) ->
      `(
        <TestResultRecommendations
          testResultId={test.id}
          sex={test.sex}
          scoreValue={test.scoreValue}
          recommendations={test.recommendations}/>
      )`

  renderSettingsMain: ->
    `(
      <div className="data data_account-main">
				<div className="data__title">Данные пациента</div>
				<div className="data__row">
					<div className="data__label">Имя</div>
					<div className="data__content">
					  <Field>
  						<Input
        		    valueLink={this.linkState('firstname')}
        		    invalid={this.state.showValidation && this.validity.children.firstname.invalid}
        		    invalidMessage={Account.errors.minLength}/>
        		</Field>
					</div>
				</div>
				<div className="data__row">
					<div className="data__label">Фамилия</div>
					<div className="data__content">
						<Field>
  						<Input
        		    valueLink={this.linkState('lastname')}/>
        		</Field>
					</div>
				</div>
				<div className="data__row">
					<div className="data__label">Email</div>
					<div className="data__content">
						<Field>
  						<Input
        		    valueLink={this.linkState('email')}
        		    invalid={this.isEmailInvalid()}
        		    invalidMessage={this.getEmailInvalidMessage()}/>
        		</Field>
					</div>
				</div>
			</div>
    )`

  renderSettingsSocial: ->
    `(
      <div className="data data_account-social">
				<div className="data__title">Данные пациента</div>
				<div className="data__row data__row_higher">
					<div className="data__label">Заходить через социальные сети</div>
					<div className="data__content">
						<ul className="social social_gray">
							<li><a className="socail__vk" href="#"><i></i></a></li>
							<li><a className="socail__fb" href="#"><i></i></a></li>
							<li><a className="socail__ok" href="#"><i></i></a></li>
						</ul>
					</div>
				</div>
				<div className="data__row">
					<div className="data__label">Получать рассылку?</div>
					<div className="data__content">
					  <div className="data__fieldset">
					    <BooleanRadioGroup valueLink={this.linkState('confirmSubscription')}/>
					   </div>
					</div>
				</div>
			</div>
		)`

  renderSettingsInfo: ->
    `(
      <div className="data data_account-info">
        <Visibility show={this.state.doctor}>
          <div className="data__title">Дополнительная информация</div>
          <div className="data__row">
            <div className="data__label">Специализация</div>
            <div className="data__content">
              <Field>
                <Input
                  valueLink={this.linkState('specialization')}/>
              </Field>
            </div>
          </div>
          <div className="data__row data__row_experiance">
            <div className="data__label">Стаж</div>
            <div className="data__content">
              <Field>
                <Input
                  valueLink={this.linkState('experience')}/>
              </Field>
            </div>
          </div>
          <div className="data__row">
            <div className="data__label">Адрес</div>
            <div className="data__content">
              <Field>
                <Input
                  valueLink={this.linkState('address')}/>
              </Field>
            </div>
          </div>
          <div className="data__row data__row_phone">
            <div className="data__label">Телефон</div>
            <div className="data__content">
              <Field>
                <Input
                  valueLink={this.linkState('phone')}/>
              </Field>
            </div>
          </div>
          <div className="data__row">
            <div className="data__label">Учебное заведение</div>
            <div className="data__content">
              <Field>
                <Input
                  valueLink={this.linkState('institution')}/>
              </Field>
            </div>
          </div>
          <div className="data__row data__row_year">
            <div className="data__label">Год окончания</div>
            <div className="data__content">
              <div className="field">
                <div className="field__in">
                  <span>{this.state.graduation && this.state.graduation.format("YYYY")}</span>
                </div>
              </div>
              <div className="date">
                <div className="date__title" onClick={this.openDoctorGraduationCalendar}>выбрать дату</div>
                <DateInput
                  ref="doctorGraduationCalendar"
                  valueLink={this.linkState('graduation')}/>
              </div>
            </div>
          </div>
        </Visibility>
				<div className="data__row data__row_btns">
					<button className="btn" onClick={this.handleSave}>Сохранить</button>
					<button className="btn" onClick={this.handleCancel}>Отменить</button>
				</div>
			</div>
		)`

  renderSettings: ->
    `(
      <div className="page">
        {this.renderSettingsMain()}
        {this.renderSettingsSocial()}
        {this.renderSettingsInfo()}
      </div>
    )`

  render: ->
    `(
      <div>
        <div className="account">	
    			<div className="account__top">
    				<div className="account__tabs">
    				  <button
    				    onClick={this.createTabChangeHandler(Account.tabs.test)}
    				    className={this.state.active == Account.tabs.test ? "btn btn_arrow is-active" : "btn btn_arrow"}
    				    >
    				    Мои рекомендации
    				    <i className="ico-arrow-down"></i>
    				  </button>
    				  <button
    				    onClick={this.createTabChangeHandler(Account.tabs.settings)}
    				    className={this.state.active == Account.tabs.settings ? "btn btn_arrow is-active" : "btn btn_arrow"}
    				    >
    				    Мои данные
    				    <i className="ico-arrow-down"></i>
    				  </button>
    				</div>
    				<button
    				  onClick={this.handleLogout}
    				  className="account__out">
    				  Выход
    				</button>
    			</div>
    		</div>
    		{this.state.active === Account.tabs.test ? this.renderTests() : this.renderSettings()}
    	</div>
    )`


module.exports = Account
