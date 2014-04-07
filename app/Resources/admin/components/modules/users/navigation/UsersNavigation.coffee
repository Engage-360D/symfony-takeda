`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"

NavigationBlock = require "Engage360d/components/navigation/NavigationBlock"

UsersNavigation = React.createClass
  getInitialState: ->
    active: false

  componentWillMount: ->
    Ctx.get("router").add [{ path: "/users/:id/edit", handler: "users.edit" }]
    Ctx.get("router").add [{ path: "/users", handler: "users" }]
    
    Ctx.get("router").on "change", (result) =>
      @setState active: result[0].handler.indexOf("users") isnt -1

  onClick: ->
    Ctx.get("router").handle "#!/users"

  render: ->
    `(
      <NavigationBlock
        active={this.state.active}
        onClick={this.onClick}
        title="Пользователи"
        icon="user"/>
    )`

module.exports = UsersNavigation
