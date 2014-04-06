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
Visibility = require "../helpers/Visibility"
Registration = require "./Registration"
Login = require "./Login"


Test = React.createClass
  mixins: [LinkedStateMixin, ValidationMixin]

  statics:
    sexValues: [
      {value: "male", text: "муж."}
      {value: "female", text: "жен."}
    ]
    birthdayMinDate: moment().subtract("years", 100)
    birthdayMaxDate: moment().subtract("years", 18)

  getInitialState: ->
    step: "first"
    showValidation: false
    loading: false
    doctor: false
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

  #getInitialState: ->
    #step: "second"
    #showValidation: false
    #loading: false
    #doctor: false
    #sex: "male"
    #birthday: moment().subtract("years", 65)
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
    component:
      firstStep: (state, childrenValidity) ->
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
        childrenValidity.acetylsalicylicDrugs.valid

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

  render: ->
    maxScoreValue = if @state.sex is "male" then 47 else 20
    scoreOffset = 0

    if @state.scoreValue
      scoreOffset = @state.scoreValue / (maxScoreValue / 100)

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
                        <BooleanRadioGroup valueLink={this.linkState('doctor')} />
                      </div>
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
                        <div className="field">
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
                  <div className="data__title">Личные данные</div>
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
                <Registration
                  user={{}}
                  showDoctor={false}
                  reloadOnRegister={false}
                  valueLink={this.linkState('registered')}/>
                <Login
                  reloadOnSuccess={false}
                  valueLink={this.linkState('logged')}/>
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
          <div className="page page_step_3">
            <div className="result">
              <div className="result__top">
                <div className="result__info"></div>
                <div className="result__arrow"></div>
              </div>
              <div className="result__val">
                <div className="result__val-blue" style={{width: scoreOffset + '%'}}><span>{this.state.scoreValue}</span></div>
                <div className="result__val-red"></div>
              </div>
              <div className="result__diagnosis">Вероятность тяжелых сердечно-сосудистых заболеваний в ближайшие 10 лет</div>
              <div className="result__text"><i className="result__attention-big"></i>Не соответствует норме. Воспользуйтесь нашими рекомендациями</div>
            </div>
          </div>
        </Visibility>
      </div>
    )`


module.exports = Test
