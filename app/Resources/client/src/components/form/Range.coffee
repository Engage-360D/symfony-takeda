`/** @jsx React.DOM */`

React = require "react"
cx = require "react/lib/cx"
$ = require "jquery"


mouseMoveHandler = null
mouseUpHandler = null

$(document).on 'mousemove', (event) ->
  if mouseMoveHandler
    mouseMoveHandler event

$(document).on 'mouseup', (event) ->
  if mouseUpHandler
    mouseUpHandler event


Range = React.createClass
  componentDidMount: ->
    @catched = false
    pointNode = @refs.point.getDOMNode()
    $(pointNode).on 'mousedown', @handleMouseDown

  handleMouseDown: (event) ->
    event.preventDefault()
    @startValue = this.props.valueLink.value
    @currentValue = this.props.valueLink.value
    mouseMoveHandler = @handleMouseMove
    mouseUpHandler = @handleMouseUp

  handleMouseMove: (event) ->
    stepSize = Number @props.step
    minValue = Number @props.min
    maxValue = Number @props.max

    $lineNode = $(@refs.line.getDOMNode())
    $pointNode = $(@refs.point.getDOMNode())

    lineWidth = $lineNode.width()
    lineOffset = $lineNode.offset().left

    position = event.pageX - lineOffset
    position = 0 if position < 0
    position = lineWidth if position > lineWidth

    stepWidth = lineWidth / ((maxValue - minValue) / stepSize)
    currentStep = Math.round(position / stepWidth)
    currentValue = currentStep * stepSize + minValue
    
    return if @currentValue is currentValue
    
    currentStepOffset = Math.round(stepWidth * currentStep)

    @currentValue = currentValue

    $pointNode.text(currentValue)
    $pointNode.css left: "#{currentStepOffset}px"

  handleMouseUp: ->
    mouseMoveHandler = null
    mouseUpHandler = null

    if @currentValue and @currentValue isnt @startValue
      @props.valueLink.requestChange @currentValue
      @currentValue = null

  render: ->
    stepSize = Number @props.step
    minValue = Number @props.min
    maxValue = Number @props.max
    stepWidth = 100 / ((maxValue - minValue) / stepSize)
    offset = ((this.props.valueLink.value - minValue) / stepSize) * stepWidth
    
    classes = cx
      "range": true
      "is-revert": !!@props.revert

    @transferPropsTo `(
			<div className={classes}>
				<div className="range__from"><span>{this.props.min}</span><i></i></div>
				<div className="range__to"><span>{this.props.max}</span><i></i></div>
				<div className="range__val" ref="line"><span style={{left: offset + '%'}} ref="point">{this.props.valueLink.value}</span></div>
				<div className="range__in">
					<div className="range__line"></div>
			  </div>
			</div>
		)`


module.exports = Range