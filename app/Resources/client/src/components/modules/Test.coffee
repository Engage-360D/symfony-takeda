`/** @jsx React.DOM */`

React = require "react"
moment = require "moment"
$ = require "jquery"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
validationConstraints = require "../../services/validationConstraints"

RadioGroup = require "../form/RadioGroup"
BooleanRadioGroup = require "../form/BooleanRadioGroup"
DateInput = require "../form/DateInput"
Range = require "../form/Range"
Input = require "../registration/Input"
NumberSelect = require "../registration/NumberSelect"
Visibility = require "../helpers/Visibility"
Registration = require "./Registration"
Login = require "./Login"
TestResultRecommendations = require "./TestResultRecommendations"


Test = React.createClass
  mixins: [LinkedStateMixin, ValidationMixin]

  statics:
    sexValues: [
      {value: "male", text: "муж."}
      {value: "female", text: "жен."}
    ]
    authTypeValues: [
      {value: "login", text: "Войти на сайт"}
      {value: "registration", text: "Зарегистрироваться"}
    ]
    birthdayMinDate: moment().subtract("years", 100)
    birthdayMaxDate: moment().subtract("years", 22)
    doctorGraduationMinDate: moment([1940, 0, 1])
    doctorGraduationMaxDate: moment().subtract("days", 1)

  getInitialState: ->
    if @props.firstname or @props.lastname
      user = 
        firstname: @props.firstname
        lastname: @props.lastname
      logined = true
    else
      user = null
      logined = false
    
    step: "first"
    showValidation: false
    loading: false
    showDoctorPopup: false
    showDoctorPopupValidation: false
    user: user
    authType: "login"
    recommendations: null
    registered: false
    logined: logined
    doctor: false
    doctorSpecialization: null
    doctorExperience: null
    doctorAddress: null
    doctorPhone: null
    doctorInstitution: null
    doctorGraduation: null
    sex: null
    birthday: null
    growth: null
    weight: null
    smoking: null
    cholesterolLevel: 4
    cholesterolDrugs: null
    diabetes: null
    sugarProblems: null
    sugarDrugs: null
    arterialPressure: 120
    arterialPressureDrugs: null
    physicalActivity: 120
    heartAttackOrStroke: null
    extraSalt: null
    acetylsalicylicDrugs: null

    #step: "second"
    #showValidation: false
    #loading: false
    #showDoctorPopup: false
    #showDoctorPopupValidation: false
    #user: user
    #authType: "login"
    #recommendations: null
    #registered: false
    #logined: logined
    #doctor: false
    #doctorSpecialization: null
    #doctorExperience: null
    #doctorAddress: null
    #doctorPhone: null
    #doctorInstitution: null
    #doctorGraduation: null
    #sex: "male"
    #birthday: moment().subtract("years", 100)
    #growth: 185
    #weight: 95
    #smoking: true
    #cholesterolLevel: 8
    #cholesterolDrugs: false
    #diabetes: true
    #sugarProblems: null
    #sugarDrugs: false
    #arterialPressure: 180
    #arterialPressureDrugs: false
    #physicalActivity: 80
    #heartAttackOrStroke: true
    #extraSalt: true
    #acetylsalicylicDrugs: false

  getValidationConfig: ->
    children:
      doctorSpecialization:
        notNull: validationConstraints.notNull()
      doctorExperience:
        notNull: validationConstraints.notNull()
      doctorAddress:
        notNull: validationConstraints.notNull()
      doctorPhone:
        notNull: validationConstraints.notNull()
      doctorInstitution:
        notNull: validationConstraints.notNull()
      doctorGraduation:
        notNull: validationConstraints.notNull()
      sex:
        notNull: validationConstraints.notNull()
      birthday:
        notNull: validationConstraints.notNull()
      growth:
        notNull: validationConstraints.notNull()
        min: validationConstraints.min(30)
        max: validationConstraints.max(300)
      weight:
        notNull: validationConstraints.notNull()
        min: validationConstraints.min(30)
        max: validationConstraints.max(700)
      smoking:
        notNull: validationConstraints.notNull()
      cholesterolDrugs:
        required: (cholesterolDrugs, state) ->
          return true if state.cholesterolLevel < 5
          validationConstraints.notNull()(cholesterolDrugs)
      diabetes:
        notNull: validationConstraints.notNull()
      sugarProblems:
        required: (sugarProblems, state) ->
          return true if state.diabetes
          validationConstraints.notNull()(sugarProblems)
      sugarDrugs:
        required: (sugarDrugs, state) ->
          return true unless state.diabetes
          validationConstraints.notNull()(sugarDrugs)
      arterialPressureDrugs:
        required: (arterialPressureDrugs, state) ->
          return true if state.arterialPressure < 140
          validationConstraints.notNull()(arterialPressureDrugs)
      heartAttackOrStroke:
        notNull: validationConstraints.notNull()
      extraSalt:
        notNull: validationConstraints.notNull()
      acetylsalicylicDrugs:
        notNull: validationConstraints.notNull()
      user:
        notNull: validationConstraints.notNull()
    component:
      doctorInfo: (state, childrenValidity) ->
        return true unless state.doctor
        childrenValidity.doctorSpecialization.valid and \
        childrenValidity.doctorExperience.valid and \
        childrenValidity.doctorAddress.valid and \
        childrenValidity.doctorPhone.valid and \
        childrenValidity.doctorInstitution.valid and \
        childrenValidity.doctorGraduation.valid
      firstStep: (state, childrenValidity) ->
        doctorInfoValid =
          childrenValidity.doctorSpecialization.valid and \
          childrenValidity.doctorExperience.valid and \
          childrenValidity.doctorAddress.valid and \
          childrenValidity.doctorPhone.valid and \
          childrenValidity.doctorInstitution.valid and \
          childrenValidity.doctorGraduation.valid
        ((state.doctor and doctorInfoValid) or not state.doctor) and \
        childrenValidity.sex.valid and \
        childrenValidity.birthday.valid and \
        childrenValidity.growth.valid and \
        childrenValidity.weight.valid and \
        childrenValidity.smoking.valid and \
        childrenValidity.cholesterolDrugs.valid and \
        childrenValidity.diabetes.valid and \
        childrenValidity.sugarProblems.valid and \
        childrenValidity.sugarDrugs.valid and \
        childrenValidity.heartAttackOrStroke.valid
      secondStep: (state, childrenValidity) ->
        childrenValidity.extraSalt.valid and \
        childrenValidity.acetylsalicylicDrugs.valid and \
        childrenValidity.user.valid
        
  handleDoctorChange: (doctor) ->
    @setState showDoctorPopup: doctor

  handleRegisteredOrLogined: (registeredOrLogined) ->
    if registeredOrLogined
      $.ajax
        cache: false
        dataType: "json"
        success: (user) =>
          @setState user: user
        url: "/api/users/me"

  saveDoctorInfo: ->
    if @validity.component.doctorInfo.valid
      @setState showDoctorPopup: false, showDoctorPopupValidation: false
    else
      @setState showDoctorPopupValidation: true

  openSecondStep: ->
    if @validity.component.firstStep.valid
      @setState step: "second", showValidation: false
    else
      @setState showValidation: true

  openThirdStep: ->
    if @validity.component.secondStep.valid
      @setState loading: true, =>
        @submitTest()
    else
      @setState showValidation: true

  submitTest: ->
    data =
      sex: @state.sex
      birthday: @state.birthday.format("YYYY-MM-DD")
      growth: @state.growth
      weight: @state.weight
      smoking: @state.smoking
      cholesterolLevel: @state.cholesterolLevel
      cholesterolDrugs: @state.cholesterolDrugs
      diabetes: @state.diabetes
      sugarProblems: @state.sugarProblems
      sugarDrugs: @state.sugarDrugs
      arterialPressure: @state.arterialPressure
      arterialPressureDrugs: @state.arterialPressureDrugs
      physicalActivity: @state.physicalActivity
      heartAttackOrStroke: @state.heartAttackOrStroke
      extraSalt: @state.extraSalt
      acetylsalicylicDrugs: @state.acetylsalicylicDrugs
      
    $.ajax
      cache: false
      data: testResult: data
      dataType: "json"
      success: @handleRequest
      type: "POST"
      url: "/api/test-results/"

  handleRequest: (testResult) ->
    @setState
      step: "third"
      showValidation: false
      loading: false
      scoreValue: testResult.scoreValue
      recommendations: testResult.recommendations

  render: ->
    user = if @state.doctor
      doctor: true
      doctorSpecialization: @state.doctorSpecialization
      doctorExperience: @state.doctorExperience
      doctorAddress: @state.doctorAddress
      doctorPhone: @state.doctorPhone
      doctorInstitution: @state.doctorInstitution
      doctorGraduation: @state.doctorGraduation
    else
      doctor: false
      doctorSpecialization: null
      doctorExperience: null
      doctorAddress: null
      doctorPhone: null
      doctorInstitution: null
      doctorGraduation: null

    `(
      <div>
        <Visibility show={this.state.step == 'first'}>
          <div className="page page_step-1">
            <div className="title title_level-2 title_center">Шаг 1. Данные пациента</div>
            <div className="layout">
              <div className="layout__column">
                <div className="data">
                  <div className="data__title">Личные данные</div>
                  <div className="data__row">
                    <div className="data__label">Вы являетесь врачом?</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <BooleanRadioGroup valueLink={this.linkState('doctor', this.handleDoctorChange)} invalid={this.state.showValidation && this.validity.component.doctorInfo.invalid} />
                      </div>
                      <Visibility show={this.state.showDoctorPopup}>
                        <div className="mainspec mainspec_popup">
                          <div className="mainspec__title">Основная специализация</div>
                          <div className="mainspec__item mainspec__add">
                            <div className="field">
                              <Input placeholder="Введите название" valueLink={this.linkState('doctorSpecialization')} invalid={this.state.showDoctorPopupValidation && this.validity.children.doctorSpecialization.invalid} />
                            </div>
                          </div>
                          <div className="mainspec__item mainspec__experience">
                            <div className="field">
                              <div className="field__label">Стаж</div>
                              <NumberSelect
                                valueLink={this.linkState('doctorExperience')}
                                invalid={this.state.showDoctorPopupValidation && this.validity.children.doctorExperience.invalid}/>
                              <div className="field__label">лет</div>
                            </div>
                          </div>
                          <div className="mainspec__item mainspec__address">
                            <div className="field">
                              <div className="field__label">Адрес</div>
                              <Input valueLink={this.linkState('doctorAddress')} invalid={this.state.showDoctorPopupValidation && this.validity.children.doctorAddress.invalid} />
                            </div>
                          </div>
                          <div className="mainspec__item mainspec__phone">
                            <div className="field">
                              <div className="field__label">Телефон</div>
                              <Input valueLink={this.linkState('doctorPhone')} invalid={this.state.showDoctorPopupValidation && this.validity.children.doctorPhone.invalid} />
                            </div>
                          </div>
                          <div className="mainspec__item mainspec__school">
                            <div className="field">
                              <div className="field__label">Учебное заведение</div>
                              <Input valueLink={this.linkState('doctorInstitution')} invalid={this.state.showDoctorPopupValidation && this.validity.children.doctorInstitution.invalid} />
                            </div>
                          </div>
                          <div className="mainspec__item mainspec__date">
                            <div className="field">
                              <div className="field__label">Год окончания</div>
                              <DateInput valueLink={this.linkState('doctorGraduation')} minDate={Test.doctorGraduationMinDate} maxDate={Test.doctorGraduationMaxDate} invalid={this.state.showDoctorPopupValidation && this.validity.children.doctorGraduation.invalid} />
                            </div>
                          </div>
                          <div className="mainspec__btn">
                            <button className="btn" onClick={this.saveDoctorInfo}>Продолжить</button>
                          </div>
                        </div>
                      </Visibility>
                    </div>
                  </div>
                </div>
                <div className="data">
                  <div className="data__title">Данные пациента</div>
                  <div className="data__row">
                    <div className="data__label">Пол</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <RadioGroup values={Test.sexValues} valueLink={this.linkState('sex')} invalid={this.state.showValidation && this.validity.children.sex.invalid} />
                      </div>
                    </div>
                  </div>
                  <div className="data__row">
                    <div className="data__label">Возраст</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <div className="field field_birthday">
                          <DateInput valueLink={this.linkState('birthday')} minDate={Test.birthdayMinDate} maxDate={Test.birthdayMaxDate} invalid={this.state.showValidation && this.validity.children.birthday.invalid} />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="data__row">
                    <div className="data__label">Рост</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <div className="field">
                          <Input valueLink={this.linkState('growth')} invalid={this.state.showValidation && this.validity.children.growth.invalid} />
                          <div className="field__label">см</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="data__row">
                    <div className="data__label">Вес</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <div className="field">
                          <Input valueLink={this.linkState('weight')} invalid={this.state.showValidation && this.validity.children.weight.invalid} />
                          <div className="field__label">кг</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="data__row">
                    <div className="data__label">Вы курите?</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <BooleanRadioGroup valueLink={this.linkState('smoking')} invalid={this.state.showValidation && this.validity.children.smoking.invalid} />
                      </div>
                    </div>
                  </div>
                  <div className="data__row">
                    <div className="data__label">Уровень общего холестирина</div>
                    <div className="data__content">
                      <Range valueLink={this.linkState('cholesterolLevel')} min="3" max="9" step="0.5" />
                    </div>
                  </div>
                  <Visibility show={this.state.cholesterolLevel >= 5}>
                    <div className="data__row">
                      <div className="data__label">Принимаете лекарства для снижения уровня холестерина?</div>
                      <div className="data__content">
                        <div className="data__fieldset">
                          <BooleanRadioGroup valueLink={this.linkState('cholesterolDrugs')} invalid={this.state.showValidation && this.validity.children.cholesterolDrugs.invalid} />
                        </div>
                      </div>
                    </div>
                  </Visibility>
                </div>
              </div>
              <div className="layout__column">
                <div className="data">
                  <div className="data__title">История пациента</div>
                  <div className="data__row">
                    <div className="data__label">Страдаете ли вы диабетом?</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <BooleanRadioGroup valueLink={this.linkState('diabetes')} invalid={this.state.showValidation && this.validity.children.diabetes.invalid} />
                      </div>
                    </div>
                  </div>
                  <Visibility hide={this.state.diabetes}>
                    <div className="data__row">
                      <div className="data__label">Отмечалось ли повышение уровня сахара в крови?</div>
                      <div className="data__content">
                        <div className="data__fieldset">
                          <BooleanRadioGroup valueLink={this.linkState('sugarProblems')} invalid={this.state.showValidation && this.validity.children.sugarProblems.invalid} />
                        </div>
                      </div>
                    </div>
                  </Visibility>
                  <Visibility show={this.state.diabetes === true}>
                    <div className="data__row">
                      <div className="data__label">Принимаете ли препараты для контроля уровня сахара в крови?</div>
                      <div className="data__content">
                        <div className="data__fieldset">
                          <BooleanRadioGroup valueLink={this.linkState('sugarDrugs')} invalid={this.state.showValidation && this.validity.children.sugarDrugs.invalid} />
                        </div>
                      </div>
                    </div>
                  </Visibility>
                  <div className="data__row data__row_wide">
                    <div className="data__label">Артериальное давление в мм рт. ст.</div>
                    <div className="data__content">
                      <Range valueLink={this.linkState('arterialPressure')} min="80" max="200" step="1" />
                    </div>
                  </div>
                  <Visibility show={this.state.arterialPressure >= 140}>
                    <div className="data__row">
                      <div className="data__label">Принимаете ли препараты для понижения давления?</div>
                      <div className="data__content">
                        <div className="data__fieldset">
                          <BooleanRadioGroup valueLink={this.linkState('arterialPressureDrugs')} invalid={this.state.showValidation && this.validity.children.arterialPressureDrugs.invalid} />
                        </div>
                      </div>
                    </div>
                  </Visibility>
                  <div className="data__row data__row_wide">
                    <div className="data__label">Сколько минут в неделю занимаетесь физической активностью</div>
                    <div className="data__content">
                      <Range valueLink={this.linkState('physicalActivity')} min="80" max="200" step="1" revert />
                    </div>
                  </div>
                  <div className="data__row">
                    <div className="data__label">Был ли у вас инфаркт/инсульт?</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <BooleanRadioGroup valueLink={this.linkState('heartAttackOrStroke')} invalid={this.state.showValidation && this.validity.children.heartAttackOrStroke.invalid} />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="step-title" onClick={this.openSecondStep}>
            <div className="step-title__in">
              <span>Шаг 2. Дневной рацион</span>
              <i className="ico-arrow-down"></i>
            </div>
          </div>
        </Visibility>
        <Visibility show={this.state.step == 'second'}>
          <div className="page page_step-2">
            <div className="title title_level-2 title_center">Шаг 2. Дневной рацион</div>
            <div className="layout">
              <div className="layout__column">
                <div className="data">
                  <div className="data__title">Дневной рацион</div>
                  <div className="data__row">
                    <div className="data__label">Досаливаете ли вы пищу?</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <BooleanRadioGroup valueLink={this.linkState('extraSalt')} invalid={this.state.showValidation && this.validity.children.extraSalt.invalid} />
                      </div>
                    </div>
                  </div>
                  <div className="data__row">
                    <div className="data__label">Принимаете ли припараты на основе ацетилсалициловой кислоты для профилактики риска тромбозов</div>
                    <div className="data__content">
                      <div className="data__fieldset">
                        <BooleanRadioGroup valueLink={this.linkState('acetylsalicylicDrugs')} invalid={this.state.showValidation && this.validity.children.acetylsalicylicDrugs.invalid} />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="layout__column">
                <Visibility hide={this.state.registered || this.state.logined}>
                  <div className="data">
                    <div className="data__title">Авторизация</div>
                    <div className="data__row">
                      <RadioGroup values={Test.authTypeValues} valueLink={this.linkState('authType')} />
                    </div>
                  </div>
                  <Visibility show={this.state.authType == 'registration'}>
                    <Registration
                      user={user}
                      showDoctor={false}
                      reloadOnRegister={false}
                      valueLink={this.linkState('registered', this.handleRegisteredOrLogined)} />
                  </Visibility>
                  <Visibility show={this.state.authType == 'login'}>
                    <Login
                      reloadOnSuccess={false}
                      valueLink={this.linkState('logined', this.handleRegisteredOrLogined)} />
                  </Visibility>
                </Visibility>
                <Visibility show={!!this.state.user}>
                  <div className="data">
                    <div className="data__title">Авторизация</div>
                    <div className="data__row">
                      Здравствуйте, {this.state.user ? [this.state.user.firstname, this.state.user.lastname].join(' ') : ''}!
                    </div>
                  </div>
                </Visibility>
              </div>
            </div>
          </div>
          <Visibility hide={this.state.loading}>
            <div className="step-title" onClick={this.openThirdStep}>
              <div className="step-title__in">
                <span>Шаг 3. Результаты</span>
                <i className="ico-arrow-down"></i>
              </div>
            </div>
          </Visibility>
        </Visibility>
        <Visibility show={this.state.step == 'third'}>
          <TestResultRecommendations sex={this.state.sex} scoreValue={this.state.scoreValue} recommendations={this.state.recommendations} />
        </Visibility>
      </div>
    )`


module.exports = Test
