`/** @jsx React.DOM */`

React = require "react"
moment = require "moment"
reqwest = require "reqwest"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
RegistrationMixin = require "../../mixins/RegistrationMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
validationConstraints = require "../../services/validationConstraints"


Checkbox = require "../form/Checkbox"
Field = require "../registration/Field"
Input = require "../registration/Input"
DateInput = require "../registration/DateInput"
Checkbox = require "../registration/Checkbox"
RegionsInput = require "../registration/RegionsInput"
NumberSelect = require "../registration/NumberSelect"
Switch = require "../registration/Switch"
FacebookButton = require "../social/login/FacebookButton"
VkontakteButton = require "../social/login/VkontakteButton"


Registration = React.createClass
  mixins: [LinkedStateMixin, ValidationMixin, RegistrationMixin]

  statics:
    context:
      base: 0
      doctor: 1
      registration: 2

  getDefaultProps: ->
    context: Registration.context.base
    user: null

  getInitialState: ->
    console.log @props.user
    showValidation: false
    showDoctorValidation: false
    context: parseInt @props.context

  componentWillMount: ->
    if @props.user
      @setState JSON.parse @props.user

  getValidationConfig: ->
    children:
      firstname:
        notBlank: validationConstraints.notBlank()
      email:
        email: validationConstraints.email()
      region:
        notBlank: validationConstraints.notBlank()
      birthday:
        date: validationConstraints.date()
      password:
        notBlank: validationConstraints.notBlank()
      confirmPassword:
        notBlank: validationConstraints.notBlank()
      specialization:
        notBlank: validationConstraints.notBlank()
      experience:
        notBlank: validationConstraints.notBlank()
      address:
        notBlank: validationConstraints.notBlank()
      phone:
        notBlank: validationConstraints.notBlank()
      institution:
        notBlank: validationConstraints.notBlank()
      graduation:
        notBlank: validationConstraints.notBlank()
    component:
      main: (state, childrenValidity) ->
        #"confirmInformation", "confirmPersonalization", "confirmSubscription"
        fields = [
          "firstname", "email", "region", "birthday", "password", "confirmPassword"
        ]
        for field in fields
          return false unless (childrenValidity[field]? and childrenValidity[field].valid)
        true
      doctor: (state, childrenValidity) ->
        return true unless state.doctor
        fields = [
          "specialization", "experience", "address",
          "phone", "institution", "graduation"
        ]

        for field in fields
          return false unless (childrenValidity[field]? and childrenValidity[field].valid)
        true

  handleDoctorChange: (value) ->
    @setState context: Registration.context.doctor if value

  handleDocktorSave: ->
    if @validity.component.doctor.invalid
      return @setState showDoctorValidation: true
    @setState context: Registration.context.registration

  handleRegistrationChange: ->
    @setState context: Registration.context.registration

  handleBackClick: ->
    @setState context: Registration.context.base

  handleRegistrationClick: ->
    if @validity.component.main.invalid
      return @setState showValidation: true
    @register()

  renderDoctorForm: ->
    `(
      <div>
        <div className="data__title">
          Ваши данные
        </div>
        <div className="data__row data__row_registration">
					<div className="data__label">Основная специализация</div>
					<div className="data__content">
					   <Input
					    valueLink={this.linkState('specialization')}
					    invalid={this.state.showDoctorValidation && this.validity.children.specialization.invalid}/>
					</div>
				</div>
				<div className="data__row data__row_registration">
					<div className="data__label">Стаж</div>
					<div className="data__content">
					   <NumberSelect
					    valueLink={this.linkState('experience')}
					    invalid={this.state.showDoctorValidation && this.validity.children.experience.invalid}/>
					</div>
				</div>
        <div className="data__row data__row_registration">
					<div className="data__label">Адрес</div>
					<div className="data__content">
					   <Input
					    valueLink={this.linkState('address')}
					    invalid={this.state.showDoctorValidation && this.validity.children.address.invalid}/>
					</div>
				</div>
				<div className="data__row data__row_registration">
					<div className="data__label">Телефон</div>
					<div className="data__content">
					   <Input
					    valueLink={this.linkState('phone')}
					    invalid={this.state.showDoctorValidation && this.validity.children.phone.invalid}/>
					</div>
				</div>
				<div className="data__row data__row_registration">
					<div className="data__label">Учебное заведение</div>
					<div className="data__content">
					   <Input
					    valueLink={this.linkState('institution')}
					    invalid={this.state.showDoctorValidation && this.validity.children.institution.invalid}/>
					</div>
				</div>
				<div className="data__row data__row_registration">
					<div className="data__label">Год окончания</div>
					<div className="data__content">
					   <NumberSelect
					    min={1950}
					    max={parseInt(moment().format("YYYY"))}
					    valueLink={this.linkState('graduation')}
					    invalid={this.state.showDoctorValidation && this.validity.children.graduation.invalid}/>
					</div>
				</div>
        <div className="enter__btn">
					<button className="btn" onClick={this.handleDocktorSave}>Продолжить</button>
				</div>
      </div>
    )`

  renderRegistration: ->
    `(
      <div>
        <div className="data__title">
          Ваши данные
        </div>
        <div className="data__row data__row_border">
					<div className="data__label">Вы являетесь врачом?</div>
					<div className="data__content">
					  <div className="data__fieldset">
					    <label className="radio">
					      <input type="radio" name="item1" valueLink={this.linkState('doctor', this.handleDoctorChange)}/>
					      <span>Да</span>
					     </label>
					    <label className="radio">
					      <input type="radio" name="item1"/>
					      <span>Нет</span>
					     </label>
					  </div>
					</div>
				</div>
				<p style={{height: "10px"}}/>
				<div className="enter">
    			<Field className="field_mod">
    			  <Input
    			    placeholder="Имя"
    			    valueLink={this.linkState('firstname')}
    			    invalid={this.state.showValidation && this.validity.children.firstname.invalid}/>
    			</Field>
    			<Field className="field_mod">
    			  <Input
    			    placeholder="Email"
    			    valueLink={this.linkState('email')}
    			    invalid={this.state.showValidation && this.validity.children.email.invalid}/>
    			</Field>
  			</div>
  			<div className="data__row data__row_registration">
					<div className="data__label">Регион</div>
					<div className="data__content">
					   <RegionsInput
					    valueLink={this.linkState('region')}
					    invalid={this.state.showValidation && this.validity.children.region.invalid}/>
					</div>
				</div>
				<div className="data__row data__row_border data__row_registration">
					<div className="data__label">Дата рождения</div>
					<div className="data__content">
					   <DateInput
					    valueLink={this.linkState('birthday')}
					    invalid={this.state.showValidation && this.validity.children.birthday.invalid}/>
					</div>
				</div>
				<div className="data__row">
					<div className="data__label">Создание пароля</div>
				</div>
				<div className="enter">
    			<Field className="field_mod">
    			  <Input
    			    type="password"
    			    placeholder="Пароль"
    			    valueLink={this.linkState('password')}
    			    invalid={this.state.showValidation && this.validity.children.password.invalid}/>
    			</Field>
    			<Field className="field_mod">
    			  <Input
    			    type="password"
    			    placeholder="Подтвердить пароль"
    			    valueLink={this.linkState('confirmPassword')}
    			    invalid={this.state.showValidation && this.validity.children.confirmPassword.invalid}/>
    			</Field>
  			</div>
        <div>Подтверждение</div>
        <Field>
          <Checkbox checkedLink={this.linkState('confirmPersonalization')}>
            согласен на обработку персональных данных
          </Checkbox>
        </Field>
        <Field>
          <Checkbox checkedLink={this.linkState('confirmSubscription')}>
            согласен получать информацию по email
          </Checkbox>
        </Field>
        <Field>
          <Checkbox checkedLink={this.linkState('confirmInformation')}>
            согласен с тем, что вся информация носит рекомендательный характер
          </Checkbox>
        </Field>
        <p style={{height: "20px"}}/>
        <div className="enter__btn">
				  <button className="btn" onClick={this.handleRegistrationClick}>
					  Зарегистрироваться
				  </button>
				</div>
      </div>
    )`

  renderBase: ->
    `(
      <div>
        <div className="data__title">
          Сохранить данные и зарегистрироваться
        </div>
				<div className="data__row data__row_social">
					<div className="data__label">Использовать аккаунт социальных сетей:</div>
					<ul className="social social_gray">
						<li><VkontakteButton/></li>
						<li><FacebookButton/></li>
						<li><a className="socail__ok" href="#"><i></i></a></li>
					</ul>
				</div>
				<button className="btn btn_arrow" onClick={this.handleRegistrationChange}>
				  Зарегистрироваться
				  <i className="ico-arrow-down"></i>
				</button>
      </div>
    )`

  renderState: ->
    if @state.context is Registration.context.doctor
      @renderDoctorForm()
    else if @state.context is Registration.context.registration
      @renderRegistration()
    else
      @renderBase()

  render: ->
    `(
      <div className="data">
				{this.renderState()}
			</div>
    )`


module.exports = Registration
