`/** @jsx React.DOM */`

React = require "react"
moment = require "moment"
reqwest = require "reqwest"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
RegistrationMixin = require "../../mixins/RegistrationMixin"
HTMLElementContainerMixin = require "../../mixins/HTMLElementContainerMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
validationConstraints = require "../../services/validationConstraints"


Modal = require "../modal/Modal"
Checkbox = require "../form/Checkbox"
Field = require "../registration/Field"
Input = require "../registration/Input"
DateInput = require "../registration/DateInput"
Checkbox = require "../registration/Checkbox"
RegionsInput = require "../registration/RegionsInput"
NumberSelect = require "../registration/NumberSelect"
BooleanRadioGroup = require "../form/BooleanRadioGroup"
FacebookButton = require "../social/login/FacebookButton"
VkontakteButton = require "../social/login/VkontakteButton"


Registration = React.createClass
  mixins: [
    LinkedStateMixin,
    ValidationMixin,
    RegistrationMixin,
    HTMLElementContainerMixin
  ]

  statics:
    context:
      base: 0
      doctor: 1
      registration: 2

  getDefaultProps: ->
    showDoctor: true
    reloadOnRegister: true
    context: Registration.context.base
    user: null

  getInitialState: ->
    doctor: false
    showValidation: false
    showDoctorValidation: false
    context: parseInt @props.context
    confirmPersonalization: true
    confirmSubscription: true
    confirmInformation: true

  componentWillMount: ->
    window.addEventListener "registrationSuccess", =>
      unless @isChildrenWindow()
        @props.valueLink.requestChange true if @props.valueLink
        @props.onRegistrationSuccess() if @props.onRegistrationSuccess
    if @props.user
      if typeof(@props.user) is "object"
        @setState @props.user
      else
        try
          @setState JSON.parse @props.user
        catch e then ->

  getValidationConfig: ->
    children:
      firstname:
        notEmpty: validationConstraints.notEmpty()
      email:
        email: validationConstraints.email()
      region:
        notEmpty: validationConstraints.notEmpty()
      password:
        notEmpty: validationConstraints.notEmpty()
      confirmPassword:
        notEmpty: validationConstraints.notEmpty()
      specialization:
        notEmpty: validationConstraints.notEmpty()
      experience:
        notEmpty: validationConstraints.notEmpty()
      address:
        notEmpty: validationConstraints.notEmpty()
      phone:
        notEmpty: validationConstraints.notEmpty()
      institution:
        notEmpty: validationConstraints.notEmpty()
      graduation:
        notEmpty: validationConstraints.notEmpty()
      confirmInformation:
        isTrue: validationConstraints.isTrue()
      confirmPersonalization:
        isTrue: validationConstraints.isTrue()
    component:
      main: (state, childrenValidity) ->
        fields = [
          "firstname", "email", "region", "password", "confirmPassword",
          "confirmInformation", "confirmPersonalization"
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
    console.log value
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
    @register (response) =>
      @showSuccess()
      state = {}
      for key, value of @state
        state[key] = null
      for key, value of @getInitialState()
        state[key] = value
      @setState state
  
  showSuccess: ->
    modal = null
    props =
      onClose: =>
        modal.setState show: false
        if @isChildrenWindow()
          event = new Event "registrationSuccess"
          window.opener.dispatchEvent event
          window.close()
        else
          @props.valueLink.requestChange true if @props.valueLink
          @props.onRegistrationSuccess() if @props.onRegistrationSuccess
          window.location.reload() if @props.reloadOnRegister
      title: "Регистрация"
      children: @renderModalBody()
    modal = React.renderComponent Modal(props), @createContainer()

  isChildrenWindow: ->
    location.href.indexOf("connect") isnt -1

  renderModalBody: ->
    `(
      <div>
        <Field className="field_mod">
        </Field>
        <Field className="field_mod">
          <span>Для подтверждения регистрации на почтовый ящик </span>
          <strong>{this.state.email}</strong> 
          <span> отправлено письмо.</span>
        </Field>
      </div>
    )`

  renderDoctorForm: ->
    `(
      <div>
        <div className="data__title">
          Сохранить данные и зарегистрироваться
        </div>
        <div className="mainspec">
					<div className="mainspec__title">Основная специализация</div>
					<div className="mainspec__item mainspec__add">
						<Field>
							<Input
							  placeholder="Введите название"
  					    valueLink={this.linkState('specialization')}
  					    invalid={this.state.showDoctorValidation && this.validity.children.specialization.invalid}/>
						</Field>
					</div>
					<div className="mainspec__item mainspec__experience">
  					<div className="field">
  						<div className="field__label">Стаж</div>
  						<NumberSelect
  					    valueLink={this.linkState('experience')}
  					    invalid={this.state.showDoctorValidation && this.validity.children.experience.invalid}/>
  						<div className="field__label">лет</div>
  					</div>
  				</div>
  				<div className="mainspec__item mainspec__address">
						<div className="field">
							<div className="field__label">Адрес</div>
							<Input
  					    valueLink={this.linkState('address')}
  					    invalid={this.state.showDoctorValidation && this.validity.children.address.invalid}/>
						</div>
					</div>
					<div className="mainspec__item mainspec__phone">
						<div className="field">
							<div className="field__label">Телефон</div>
							<Input
  					    valueLink={this.linkState('phone')}
  					    invalid={this.state.showDoctorValidation && this.validity.children.phone.invalid}/>
						</div>
					</div>
					<div className="mainspec__item mainspec__school">
						<div className="field">
							<div className="field__label">Учебное заведение</div>
							<Input
  					    valueLink={this.linkState('institution')}
  					    invalid={this.state.showDoctorValidation && this.validity.children.institution.invalid}/>
						</div>
					</div>
					<div className="mainspec__item mainspec__date">
						<div className="field">
							<div className="field__label">Год окончания</div>
							<NumberSelect
  					    min={1950}
  					    max={parseInt(moment().format("YYYY"))}
  					    valueLink={this.linkState('graduation')}
  					    invalid={this.state.showDoctorValidation && this.validity.children.graduation.invalid}/>
						</div>
					</div>
				</div>
        <div className="enter__btn">
					<button className="btn" onClick={this.handleDocktorSave}>Продолжить</button>
				</div>
      </div>
    )`

  renderDoctor: ->
    return unless @props.showDoctor
    
    `(
      <div className="data__row data__row_border">
				<div className="data__label">Вы являетесь врачом?</div>
				<div className="data__content">
				  <div className="data__fieldset">
				    <BooleanRadioGroup valueLink={this.linkState('doctor', this.handleDoctorChange)} />
				  </div>
				</div>
			</div>
		)`

  renderSocialButton: ->
    return if @isChildrenWindow()

    `(
  	  <button className="btn btn_arrow is-active" onClick={this.handleBackClick}>
  		  Через соц. сети
  		  <i className="ico-arrow-down"></i>
  		</button>
		)`

  renderRegistration: ->
    `(
      <div className="reg">
        <div className="data__title">
          Ваши данные
        </div>
        {this.renderDoctor()}
        <p style={{height: "10px"}}/>
				{this.renderSocialButton()}
				<div className="reg__in">
  				<div className="reg__title">Ваши данные</div>
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
      		<div className="reg__region">
      		  <Field>
      		    <div className="field__label">Регион</div>
      		    <div className="select">
      		      <RegionsInput
    					    valueLink={this.linkState('region')}
    					    invalid={this.state.showValidation && this.validity.children.region.invalid}/>
      		    </div>
      		  </Field>
      		</div>
    			<Field>
      			 <Input
      			  type="password"
      			  placeholder="Пароль"
      			  valueLink={this.linkState('password')}
      			  invalid={this.state.showValidation && this.validity.children.password.invalid}/>
      		</Field>
      		<Field>
      		  <Input
      		    type="password"
      		    placeholder="Подтвердить пароль"
      		    valueLink={this.linkState('confirmPassword')}
      		    invalid={this.state.showValidation && this.validity.children.confirmPassword.invalid}/>
      		</Field>
          <div className="reg__fieldset">
						<Checkbox checkedLink={this.linkState('confirmPersonalization')}>
              Согласен на обработку персональных данных
            </Checkbox>
            <Checkbox checkedLink={this.linkState('confirmSubscription')}>
              Согласен получать информацию по email
            </Checkbox>
            <Checkbox checkedLink={this.linkState('confirmInformation')}>
              Согласен с тем, что вся информация носит рекомендательный характер
            </Checkbox>
					</div>
          <div className="reg__btn">
					  <button className="btn" onClick={this.handleRegistrationClick}>
					    Зарегистрироваться 
					  </button>
					</div>
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
				<button className="btn btn_arrow is-active" onClick={this.handleRegistrationChange}>
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
