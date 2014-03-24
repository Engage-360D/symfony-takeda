`/** @jsx React.DOM */`

React = require "react"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
validationConstraints = require "../../services/validationConstraints"

Field = require "../form/Field"
Label = require "../form/Label"
Checkbox = require "../form/Checkbox"
RadioGroup = require "../form/RadioGroup"


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
      doctor:
        isTrue: validationConstraints.isTrue()
      sex:
        notBlank: validationConstraints.notBlank()
    component:
      firstStep: (state, childrenValidity) ->
        childrenValidity.doctor.valid and childrenValidity.sex.valid

  componentDidValidated: (prevValidity) ->
    if prevValidity.component.firstStep.invalid and @validity.component.firstStep.valid
      @setState step: "second"

  render: ->
    `(
      <div>
        <h2>Анализ риска</h2>
        <div>
          <div style={{display: this.state.step == 'first' ? 'block' : 'none'}}>
            <h3>Шаг 1. Данные пациента</h3>
            <fieldset>
              <legend>Личные данные</legend>
              <Field mods="OneLine">
                <Label>Вы являетесь врачом?</Label>
                <Checkbox checkedLink={this.linkState('doctor')} />
              </Field>
            </fieldset>
            <fieldset>
              <legend>Данные пациента</legend>
              <Field mods="OneLine">
                <Label>Пол</Label>
                <RadioGroup values={Test.sexValues} valueLink={this.linkState('sex')} />
              </Field>
            </fieldset>
          </div>
          <div style={{display: this.state.step == 'second' ? 'block' : 'none'}}>
            <h3>Шаг 2. Дневной рацион</h3>
          </div>
        </div>
      </div>
    )`


module.exports = Test
