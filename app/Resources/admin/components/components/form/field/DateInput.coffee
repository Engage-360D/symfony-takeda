`/** @jsx React.DOM */`

React = require "react"
Pikaday = require "pikaday"

DateInput = React.createClass
  statics:
    i18n:
      previousMonth : 'Назад'
      nextMonth     : 'Вперед'
      months        : ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
      weekdays      : ['Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота'],
      weekdaysShort : ['Вск','Пнд','Вт','Ср','Чт','Пт','Сб']

  getDefaultProps: ->
    invalid: false

  componentDidMount: ->
    picker = new Pikaday
      field: @refs.input.getDOMNode()
      i18n: DateInput.i18n
      format: "DD.MM.YYYY"
      onSelect: =>
        if @props.onChange
          @props.onChange target: value: picker.getMoment().format("DD.MM.YYYY")

  renderInput: ->
    @transferPropsTo `(
      <input className="InputElement" type="text" ref="input"/>
    )`

  render: ->
    `(
      <div className="DateInput">
        {this.renderInput()}
      </div>
    )`


module.exports = DateInput
