`/** @jsx React.DOM */`

React = require "react"
moment = require "moment"
require "moment/lang/ru"
cx = require "react/lib/cx"


DateInput = React.createClass
  getInitialState: ->
    currentMoment: @readCurrentMoment @props
    opened: false
    minDate: @props.minDate or moment().subtract('years', 100)
    maxDate: @props.maxDate or moment().add('years', 100)

  componentWillReceiveProps: (newProps) ->
    @setState
      currentMoment: @readCurrentMoment newProps
      minDate: newProps.minDate or moment().subtract('years', 100)
      maxDate: newProps.maxDate or moment().add('years', 100)

  readCurrentMoment: (props) ->
    currentMoment = if props.valueLink?.value
      moment(props.valueLink.value)
    else
      moment()
      
    if props.maxDate and currentMoment.isAfter(props.maxDate)
      currentMoment = moment(props.maxDate)

    if props.minDate and currentMoment.isBefore(props.minDate)
      currentMoment = moment(props.minDate)

    currentMoment

  open: ->
    @setState
      opened: true

  close: ->
    @setState
      opened: false
      currentMoment: @readCurrentMoment @props
      
  formatMonth: (month) ->
    month[0].toUpperCase() + month.slice(1).toLowerCase()

  setCurrentMoment: (currentMoment) ->
    @setState
      currentMoment: currentMoment

  requestChange: ->
    @props.valueLink.requestChange moment(@state.currentMoment)
    @setState
      opened: false
    if typeof @props.changeCallback is 'function' then @props.changeCallback(moment(@state.currentMoment))


  calculateVisibilityStyle: (date) ->
    visibility: if date.isAfter(@state.maxDate) or date.isBefore(@state.minDate)
      "hidden"
    else
      "visible"

  render: ->
    currentMoment = @state.currentMoment
    previousMonth = moment(currentMoment).subtract('months', 1)
    previousDate = moment(currentMoment).subtract('days', 1)
    previousYear = moment(currentMoment).subtract('years', 1)
    nextMonth = moment(currentMoment).add('months', 1)
    nextDate = moment(currentMoment).add('days', 1)
    nextYear = moment(currentMoment).add('years', 1)

    classes = cx
      "calendar": true
      "is-open": @state.opened
      "is-error": @props.invalid

    @transferPropsTo `(
      <div className={classes}>
  			<div className="calendar__ico" onClick={this.open}></div>
  			<div className="calendar__in">
  				<button className="calendar__close" onClick={this.close}></button>
  				<div className="calendar__title">{this.props.calendarTitle}</div>
  				<div className="calendar__date">
  					<div className="calendar__row">
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(previousDate)}>
  							<button className="calendar__up" onClick={this.setCurrentMoment.bind(this, previousDate)}></button>
  						</div>
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(previousMonth)}>
  							<button className="calendar__up" onClick={this.setCurrentMoment.bind(this, previousMonth)}></button>
  						</div>
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(previousYear)}>
  							<button className="calendar__up" onClick={this.setCurrentMoment.bind(this, previousYear)}></button>
  						</div>
  					</div>
  					<div className="calendar__row">
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(previousDate)}>{previousDate.format('DD')}</div>
  						<div className="calendar__cell calendar__cell_left" style={this.calculateVisibilityStyle(previousMonth)}>{this.formatMonth(previousMonth.format('MMMM'))}</div>
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(previousYear)}>{previousYear.format('YYYY')}</div>
  					</div>
  					<div className="calendar__row is-active">
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(previousDate)}>{currentMoment.format('DD')}</div>
  						<div className="calendar__cell calendar__cell_left" style={this.calculateVisibilityStyle(previousMonth)}>{this.formatMonth(currentMoment.format('MMMM'))}</div>
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(previousYear)}>{currentMoment.format('YYYY')}</div>
  					</div>
  					<div className="calendar__row">
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(nextDate)}>{nextDate.format('DD')}</div>
  						<div className="calendar__cell calendar__cell_left" style={this.calculateVisibilityStyle(nextMonth)}>{this.formatMonth(nextMonth.format('MMMM'))}</div>
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(nextYear)}>{nextYear.format('YYYY')}</div>
  					</div>
  					<div className="calendar__row">
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(nextDate)}>
  							<button className="calendar__down" onClick={this.setCurrentMoment.bind(this, nextDate)}></button>
  						</div>
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(nextMonth)}>
  							<button className="calendar__down" onClick={this.setCurrentMoment.bind(this, nextMonth)}></button>
  						</div>
  						<div className="calendar__cell" style={this.calculateVisibilityStyle(nextYear)}>
  							<button className="calendar__down" onClick={this.setCurrentMoment.bind(this, nextYear)}></button>
  						</div>
  					</div>
  				</div>
  				<div className="calendar__footer">
  					<button className="btn" onClick={this.requestChange}>Выбрать</button>
  				</div>
  			</div>
  		</div>
    )`


module.exports = DateInput
