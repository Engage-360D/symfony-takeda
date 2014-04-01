`/** @jsx React.DOM */`

React = require "React"

NavigationItem = require "Engage360d/components/navigation/NavigationItem"
NavigationSubItem = require "Engage360d/components/navigation/NavigationSubItem"

NavigationBlock = React.createClass
  getDefaultProps: ->
    active: false
    expanded: false
    title: null
    icon: null
    links: []

  getInitialState: ->
    expanded: @props.expanded

  isExpandable: ->
    @props.links.length > 0

  onExpand: (event) ->
    event.preventDefault()
    if @isExpandable()
      @setState expanded: not @state.expanded
    else
      @props.onClick() if @props.onClick

  getNavigationItemMods: ->
    mods = []
    mods.push "Active" if @props.active
    mods.push "Expandable" if @isExpandable()
    mods.push "Expanded" if @state.expanded
    mods

  renderLinks: ->
    return unless @state.expanded
    @props.links.map (link) ->
      `(
        <NavigationSubItem
          id={link.id}
          title={link.title}
          url={link.url}
          icon={link.icon}
          mods={link.active ? "Active" : ""}
          actions={link.actions}
        />
      )`

  render: ->
    `(
      <div className="NavigationBlock">
        <NavigationItem
          onClick={this.onExpand}
          mods={this.getNavigationItemMods()}
          title={this.props.title}
          icon={this.props.icon}
        />
        {this.renderLinks()}
      </div>
    )`

module.exports = NavigationBlock
