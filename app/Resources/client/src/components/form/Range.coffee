`/** @jsx React.DOM */`

React = require "react"
cx = require "react/lib/cx"
$ = require "jquery"
Visibility = require "../helpers/Visibility"

mouseMoveHandler = null
mouseUpHandler = null

$(document).on 'mousemove touchmove', (event) ->
  if mouseMoveHandler
    mouseMoveHandler event

$(document).on 'mouseup touchend', (event) ->
  if mouseUpHandler
    mouseUpHandler event


Range = React.createClass
  componentDidMount: ->
    @catched = false
    pointNode = @refs.point.getDOMNode()
    $(pointNode).on 'mousedown touchstart', @handleMouseDown

  handleMouseDown: (event) ->
    event.preventDefault()
    @startValue = this.props.valueLink.value
    @currentValue = this.props.valueLink.value
    mouseMoveHandler = @handleMouseMove
    mouseUpHandler = @handleMouseUp

    if event.type is "touchstart"
      $(@refs.point.getDOMNode()).addClass 'touch'

  handleMouseMove: (event) ->
    event.preventDefault()
    stepSize = Number @props.step
    minValue = Number @props.min
    maxValue = Number @props.max

    $lineNode = $(@refs.line.getDOMNode())
    $pointNode = $(@refs.point.getDOMNode())

    lineWidth = $lineNode.width()
    lineOffset = $lineNode.offset().left

    pageX = if 'originalEvent' in event
      event.originalEvent.pageX
    else if 'pageX' in event
      event.pageX
    else
      event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft

    position = pageX - lineOffset
    if position < 0
      position = if @props.extraOption isnt undefined then -50 else 0
    position = lineWidth if position > lineWidth

    stepWidth = lineWidth / ((maxValue - minValue) / stepSize)
    currentStep = Math.round(position / stepWidth)
    currentValue = currentStep * stepSize + minValue
    
    return if @currentValue is currentValue
    
    currentStepOffset = Math.round(stepWidth * currentStep)

    @currentValue = currentValue

    $pointNode.text if @currentValue >= @props.min then @currentValue else '-'
    $pointNode.css left: "#{currentStepOffset}px"

  handleMouseUp: ->
    mouseMoveHandler = null
    mouseUpHandler = null

    if @currentValue < @props.min
      @currentValue = false

    if @currentValue isnt null and @currentValue isnt @startValue
      @props.valueLink.requestChange @currentValue
      @currentValue = null

    if event.type is "touchend"
      $(@refs.point.getDOMNode()).removeClass 'touch'

  render: ->
    stepSize = Number @props.step
    minValue = Number @props.min
    maxValue = Number @props.max
    stepWidth = 100 / ((maxValue - minValue) / stepSize)
    offset = ((this.props.valueLink.value - minValue) / stepSize) * stepWidth
    if @props.extraOption isnt undefined and @props.valueLink.value is false
      offset = -49

    classes = cx
      "range": true
      "is-revert": !!@props.revert

    @transferPropsTo `(
			<div className={classes}>
			  <Visibility show={this.props.extraOption !== undefined}>
			    <div className="range__min_allowed"><span>{this.props.extraOption}</span><i></i></div>
			  </Visibility>
				<div className="range__from"><span>{this.props.min}</span><i></i></div>
				<div className="range__to"><span>{this.props.max}</span><i></i></div>
				<div className="range__val" ref="line"><a href="#" style={{left: offset + '%'}} ref="point">{this.props.valueLink.value !== false ? this.props.valueLink.value : '-'}</a></div>
				<div className="range__in">
					<div className="range__line"></div>
			  </div>
			</div>
		)`


module.exports = Range