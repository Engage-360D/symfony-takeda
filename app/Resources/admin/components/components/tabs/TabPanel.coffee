`/** @jsx React.DOM */`

React = require "React"

TabPanel = React.createClass
  getDefaultProps: ->
    active: 0

  getInitialState: ->
    active: @props.active
    tabs: @props.children.map (tab) ->
      tab.props.title

  #componentDidMount: ->
  #  @setActive @state.active

  #componentDidUpdate: (props, state) ->
  #  if state.active isnt @state.active
  #    @setActive @state.active

  createActiveChangeHandler: (index) ->
    => @setState active: index

  setActive: (index) ->
    for tab, idx in @props.children
      if idx is index
        tab.setState active: true
      else
        tab.setState active: false

  renderTabs: ->
    @state.tabs.map (tab, index) =>
      classes = ["Tab"]
      classes.push "Tab-Active" if @state.active is index
      handler = @createActiveChangeHandler index

      `(
        <div className={classes.join(" ")} onClick={handler}>
          {tab}
        </div>
      )`

  renderActive: ->
    active = @state.active
    @props.children.map (tab, index) =>
      `(
        <div  className={active == index ? "TabContent TabContent-Active" : "TabContent"}>
          {tab}
        </div>
      )`

  render: ->
    active = @state.active
    `(
      <div className="TabPanel">
        <div className="TabContainer">
          {this.renderTabs()}
        </div>
        {this.renderActive()}
      </div>
    )`

module.exports = TabPanel
