`/** @jsx React.DOM */`

React = require "react"
require "jstree"

Tree = React.createClass
  getDefaultProps: ->
    rootNode: {}

  componentDidMount: (domNode) ->
    tree = $ domNode
    tree.on "select_node.jstree", (event, data) =>
      @props.onSelect data if @props.onSelect

    return unless @props.rootNode.id
    tree.jstree core: data: [@props.rootNode]

  componentDidUpdate: (props, state) ->
    if props.rootNode isnt @props.rootNode
      domNode = @getDOMNode()
      $(domNode).jstree(true).destroy() if $(domNode).jstree(true)
      $(domNode).jstree core: data: [@props.rootNode]
      $(domNode).on "select_node.jstree", (event, data) =>
        @props.onSelect data if @props.onSelect

  render: ->
    `(
      <div/>
    )`

module.exports = Tree
