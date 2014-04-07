`/** @jsx React.DOM */`

React = require "react"
Pikaday = require "pikaday"
moment = require "moment"

DateInput = React.createClass
  statics:
    i18n:
      previousMonth : 'Назад'
      nextMonth     : 'Вперед'
      months        : ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
      weekdays      : ['Воскресенье','Понедельник','Вторник','Среда','Четверг','Пятница','Суббота'],
      weekdaysShort : ['Вск','Пнд','Вт','Ср','Чт','Пт','Сб']
    dateFormat: 'DD.MM.YYYY'

  getDefaultProps: ->
    invalid: false
    minDate: moment().subtract("years", 100)
    maxDate: moment().add("years", 100)

  getInitialState: ->
    invalid: @props.invalid
    value: if @props.valueLink?.value then @props.valueLink?.value.format(DateInput.dateFormat) else ""

  componentDidMount: ->
    @picker = new Pikaday
      field: @refs.input.getDOMNode()
      i18n: DateInput.i18n
      format: DateInput.dateFormat
      minDate: @props.minDate.toDate()
      maxDate: @props.maxDate.toDate()
      onSelect: =>
        @props.valueLink.requestChange @picker.getMoment()

  componentWillReceiveProps: (nextProps) ->
    @setState
      invalid: nextProps.invalid
      value: if nextProps.valueLink?.value then nextProps.valueLink?.value.format(DateInput.dateFormat) else ""

  handleChange: (event) ->
    value = event.target.value
    valid = /^\d{2}\.\d{2}\.\d{4}$/.test value

    @setState
      invalid: not valid
      value: value

    if valid
      @picker.setMoment moment(value, DateInput.dateFormat)

  handleBlur: (event) ->
    value = event.target.value
    valid = /^\d{2}\.\d{2}\.\d{4}$/.test value

    unless valid
      @setState
        invalid: false
        value: @props.valueLink.value.format(DateInput.dateFormat)

  renderInput: ->
    @transferPropsTo `(
      <input type="text" ref="input" value={this.state.value} onChange={this.handleChange} onBlur={this.handleBlur} valueLink={null} />
    )`

  render: ->
    classes = ["field__in"]
    classes.push "field__in_invalid" if @state.invalid
    `(
      <div className={classes.join(" ")}>
        {this.renderInput()}
      </div>
    )`


module.exports = DateInput
