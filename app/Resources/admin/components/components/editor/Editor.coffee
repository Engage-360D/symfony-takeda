`/** @jsx React.DOM */`

React = require "react"
$ = require "jquery"
require "sirTrevor"
LinkedValueUtils = require "react/lib/LinkedValueUtils"

Editor = React.createClass
  getDefaultProps: ->
    blockTypes: ["Text"]

  getInitialState: ->
    id: null

  componentDidMount: ->
    editor = new SirTrevor.Editor
      el: $ @refs.editor.getDOMNode()
      blockTypes: @props.blockTypes

    editor.$form.on 'keyup click focus submit', =>
      SirTrevor.onBeforeSubmit()
      data = @updateData editor.dataStore.data
      @props.valueLink.requestChange data if @props.valueLink

  updateData: (data) ->
    updated = []
    for item, index in data
      updated.push $.extend item, position: index
    updated

  render: ->
    `(
      <form className="Editor">
        <textarea ref="editor">
          {JSON.stringify({"data": LinkedValueUtils.getValue(this) || []})}
        </textarea>
      </form>
    )`

module.exports = Editor
