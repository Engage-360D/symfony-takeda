`/** @jsx React.DOM */`

React = require "React"
$ = require "jquery"
require "sirTrevor"

Editor = React.createClass
  getDefaultProps: ->
    blocks: []
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
      @props.onChange data if @props.onChange

  updateData: (data) ->
    updated = []
    for item, index in data
      updated.push $.extend item, position: index
    updated

  render: ->
    `(
      <form className="Editor">
        <textarea ref="editor">
          {JSON.stringify({"data":this.props.blocks || []})}
        </textarea>
      </form>
    )`

module.exports = Editor
