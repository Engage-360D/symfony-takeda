`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"
ModsMixin = require "Engage360d/mixins/ModsMixin"
Icon = require "Engage360d/components/icon/Icon"

NavigationSubItem = React.createClass
  mixins: [ModsMixin]

  getDefaultProps: ->
    title: null
    url: "#"
    icon: null
    actions: []

  onClick: (event) ->
    return if event.target.tagName is "I"
    event.preventDefault()
    Ctx.get("router").handle @props.url

  createClickActionHandler: (action) ->
    return unless action.onClick
    => action.onClick @props.id

  renderActions: ->
    return unless @props.actions
    @props.actions.map (action) =>
      handler = @createClickActionHandler action
      `(
        <a href={action.url || "#"} onClick={handler}>
          <Icon name={action.icon}/>
        </a>
      )`
      

  render: ->
    @transferPropsTo(
      `(
        <span className={this.getClassName("NavigationSubItem")} onClick={this.onClick}>
          <a href={this.props.url}>
            <Icon name={this.props.icon || "caret-right"}/>
            {this.props.title}
          </a>
          <span className="NavigationSubItemActions">
            {this.renderActions()}
          </span>
        </span>
      )`
    )

module.exports = NavigationSubItem
