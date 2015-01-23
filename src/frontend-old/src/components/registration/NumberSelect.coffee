`/** @jsx React.DOM */`

React = require "react"
moment = require "moment"
$ = require "jquery"

NumberSelect = React.createClass
  getDefaultProps: ->
    min: 1
    max: 70
    options: []
    value: null
    invalid: false

  componentDidMount: ->
    options = []
    order = 0
    for year in [@props.min..@props.max]
      options.push text: year, value: year, order: order++

    $selectize = $(@refs.select.getDOMNode()).selectize
      sortField: 'order'
      maxItems: 1
      options: options

    $selectize[0].selectize.on "change", (event) =>
      @props.valueLink.requestChange event

  renderSelect: ->
    @transferPropsTo `(
      <select ref="select"/>
    )`

  render: ->
    classes = ["field__in"]
    classes.push "field__in_invalid" if @props.invalid
    `(
      <div className={classes.join(" ")}>
        {this.renderSelect()}
      </div>
    )`

module.exports = NumberSelect
