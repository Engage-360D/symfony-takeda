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
        @props.valueLink.requestChange picker.getMoment().format "DD.MM.YYYY"

  renderInput: ->
    @transferPropsTo `(
      <input className="DateInput" type="text" ref="input"/>
    )`

  render: ->
    classes = ["field__in"]
    classes.push "field__in_invalid" if @props.invalid
    `(
      <div className={classes.join(" ")}>
        {this.renderInput()}
      </div>
    )`


module.exports = DateInput
