`/** @jsx React.DOM */`

React = require "React"

Ctx = require "Engage360d/services/Context"

Navigation = React.createClass
  getInitialState: ->
    show: true

  componentWillMount: ->
    Ctx.get("eventBus").on "navigation.toggle", =>
      @setState show: not @state.show
    
  render: ->
    `(
      <div className="Navigation" style={{display: this.state.show ? "table-cell" : "none"}}>
        <div className="NavigationTitle">
          {this.props.title}
        </div>
        {this.props.children}
      </div>
    )`

module.exports = Navigation
