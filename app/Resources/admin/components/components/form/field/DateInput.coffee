`/** @jsx React.DOM */`

React = require "react"
Pikaday = require "pikaday"

DateInput = React.createClass
  statics:
    dateFormat: 'DD.MM.YYYY'
    i18n:
      previousMonth : 'Назад'
      nextMonth     : 'Вперед'
      months        : ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
      weekdays      : ['Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота'],
      weekdaysShort : ['Вск','Пнд','Вт','Ср','Чт','Пт','Сб']

  getDefaultProps: ->
    invalid: false

  getInitialState: ->
    value: if @props.valueLink?.value then @props.valueLink?.value.format(DateInput.dateFormat) else ""

  componentDidMount: ->
    @picker = new Pikaday
      field: @refs.input.getDOMNode()
      i18n: DateInput.i18n
      format: DateInput.dateFormat
      onSelect: =>
        @props.valueLink.requestChange @picker.getMoment()

  componentWillReceiveProps: (nextProps) ->
    @setState
      value: if nextProps.valueLink?.value then nextProps.valueLink?.value.format(DateInput.dateFormat) else ""

  handleChange: (event) ->
    value = event.target.value
    valid = /^\d{2}\.\d{2}\.\d{4}$/.test value

    @setState
      value: value

    if valid
      @picker.setMoment moment(value, DateInput.dateFormat)

  renderInput: ->
    @transferPropsTo `(
      <input
        type="text"
        value={this.state.value}
        onChange={this.handleChange}
        className="InputElement"
        valueLink={null}
        ref="input"/>
    )`

  render: ->
    `(
      <div className="DateInput">
        {this.renderInput()}
      </div>
    )`


module.exports = DateInput
