`/** @jsx React.DOM */`

React = require "React"
require "jquery.ui.datepicker"

DateInput = React.createClass
  getDefaultProps: ->
    datepicker: {}

  getInitialState: ->
    datepicker = @props.datepicker

    datepicker.dateFormat = "dd.mm.yy" unless datepicker.dateFormat
    datepicker.firstDay = 1
    datepicker.showAnim = ""
    #datepicker.showOn = "both"
    #datepicker.buttonImage = "/common/img/engage/date-ico.png"
    #datepicker.buttonImageOnly = true
    datepicker.onSelect = (data) =>
      @props.onChange target: value: data if @props.onChange

    datepicker: datepicker

  componentDidMount: ->
    $(@refs.input.getDOMNode()).datepicker @state.datepicker

  render: ->
    `(
      <span className="DateInput">
        {this.transferPropsTo(
          <input className="InputElement" ref="input"/>
        )}
      </span>
    )`

module.exports = DateInput
