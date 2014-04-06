`/** @jsx React.DOM */`

React = require "react"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
validationConstraints = require "../../services/validationConstraints"

Checkbox = require "../form/Checkbox"
RadioGroup = require "../form/RadioGroup"
BooleanRadioGroup = require "../form/BooleanRadioGroup"
Visibility = require "../helpers/Visibility"


Test = React.createClass
  mixins: [LinkedStateMixin, ValidationMixin]

  statics:
    sexValues: [
      {value: "male", text: "Мужчина"}
      {value: "female", text: "Женщина"}
    ]

  getInitialState: ->
    doctor: false
    sex: null
    step: "first"

  getValidationConfig: ->
    children:
      sex:
        notBlank: validationConstraints.notBlank()
    component:
      firstStep: (state, childrenValidity) ->
        childrenValidity.sex.valid

  openSecondStep: ->
    if @validity.component.firstStep.valid
      @setState step: "second"

  render: ->
    `(
      <div>
        <Visibility show={this.state.step == 'first'}>
          <div className="page">
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
                        <RadioGroup values={Test.sexValues} valueLink={this.linkState('sex')} />
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
          <div className="page">
            <div className="title title_level-2 title_center">Шаг 2. Дневной рацион</div>
          </div>
        </Visibility>
      </div>
    )`


module.exports = Test
