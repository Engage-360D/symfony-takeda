`/** @jsx React.DOM */`

React = require "react"
$ = require "jquery"
compareRegions = require "../../util/compareRegions"

RegionsInput = React.createClass
  getDefaultProps: ->
    options: []
    value: null
    invalid: false

  componentDidMount: ->
    script = document.createElement('script')
    callbackName = "yaMapLoaded"
    packages = ['package.full,regions']

    window[callbackName] = => @mapLoaded()

    script.src = [
      "http://api-maps.yandex.ru/2.0/?"
      "&load=#{packages.join(',')}"
      "&lang=ru-RU"
      "&onload=#{callbackName}"
    ].join ""

    document.getElementsByTagName('head')[0].appendChild(script)

    $selectize = $(@refs.select.getDOMNode()).selectize
      sortField: 'order'
      maxItems: 1
    selectize = $selectize[0].selectize

    $selectize.on "change", (event) =>
      @props.valueLink.requestChange event.target.value

  mapLoaded: ->
    ymaps.regions.load("RU",
      lang: "ru"
      quality: 1
    ).then (result) =>
      options = []
      result.geoObjects.each (region, index) =>
        options.push
          text: region.properties.get "hintContent"
          value: region.properties.get "hintContent"
          order: index

      # Sort the region list
      options = options.sort(compareRegions)
      opt.order = i for opt, i in options

      selectize = $(@refs.select.getDOMNode())[0].selectize
      selectize.clearOptions()
      selectize.addOption options
      selectize.refreshOptions false

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

module.exports = RegionsInput
