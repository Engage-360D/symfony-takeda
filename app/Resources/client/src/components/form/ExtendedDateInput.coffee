`/** @jsx React.DOM */`

React = require "react"
DateInput = require "./DateInput"

ExtendedDateInput = React.createClass
  getInitialState: ->
    placeholder: 'error'
    format: if @props.format then @props.format else 'YYYY'

  openCalendar: ->
    console.log this.props.valueLink
    @refs.DateInput.open()

  handlePlaceholderChange: (moment) ->
    if moment and moment._isAMomentObject
      @setState placeholder: moment.format(@state.format)

  componentWillMount: ->
    if @props.valueLink.value and @props.valueLink.value._isAMomentObject
      @setState placeholder: @props.valueLink.value.format(@state.format)
    else
      @setState placeholder: @props.placeholder

  render: ->
    `(
      <div className="date">
        <div className="date__title" onClick={this.openCalendar}>{this.state.placeholder}</div>
        <DateInput
          ref="DateInput"
          changeCallback={this.handlePlaceholderChange}
          calendarTitle={this.props.calendarTitle}
          valueLink={this.props.valueLink}
          minDate={this.props.minDate}
          maxDate={this.props.maxDate}
          invalid={this.props.invalid}/>
      </div>
    )`

module.exports = ExtendedDateInput