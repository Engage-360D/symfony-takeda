`/** @jsx React.DOM */`

React = require "react"
require "selectize"

Select = React.createClass
  getDefaultProps: ->
    options: []
    value: null

  componentDidMount: ->
    $selectize = $(@refs.select.getDOMNode()).selectize
      maxItems: 1
      sortField: 'order'

    selectize = $selectize[0].selectize
        
    selectize.addOption @props.options if @props.options
    selectize.refreshOptions false
    @setValue selectize
        
    $selectize.on "change", (event) =>
      @props.onChange event if @props.onChange

  componentDidUpdate: (props, state) ->
    selectize = $(@refs.select.getDOMNode())[0].selectize

    unless @props.options and @props.options.length is props.options.length
      selectize.clearOptions()
      selectize.addOption @props.options if @props.options
      selectize.refreshOptions false
      @setValue selectize

    @setValue selectize if @props.value isnt props.value

  setValue: (selectize) ->
    if @props.value
      @value = @props.value
      selectize.setValue @value
    else if @props.isFirstDefault && @props.options.length > 0
      @value = @props.options[0].value
      selectize.setValue @value

  render: ->
    @transferPropsTo(
      `(
        <div className="Select">
          <select ref="select"/>
        </div>
      )`
    )

module.exports = Select
