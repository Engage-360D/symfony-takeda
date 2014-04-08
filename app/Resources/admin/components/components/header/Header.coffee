`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"
Icon = require "Engage360d/components/icon/Icon"
Logo = require "Engage360d/components/logo/Logo"
Button = require "Engage360d/components/button/Button"
ButtonGroup = require "Engage360d/components/button/ButtonGroup"

Header = React.createClass
  getInitialState: ->
    user: null

  componentWillMount: ->
    $.oajax
      url: "/api/users/me"
      jso_provider: "engage360d",
      jso_allowia: true
      dataType: "json"
      success: (data) =>
        @setState user: data

  getUserData: ->
    return unless @state.user
    [@state.user.firstname, @state.user.lastname].join(" ")

  logout: ->
    Ctx.get("auth").logout()

  toggleNavigation: ->
    Ctx.get("eventBus").emit "navigation.toggle"

  render: ->
    `(
      <div className="Header">
        <Logo/>
        <Button mods={["Header", "MenuToggle"]} onClick={this.toggleNavigation}>
          <Icon name="bars"/>
        </Button>
        <div className="HeaderNav">
          <ButtonGroup button={this.getUserData()} mods={["Header", "Right"]}>
            <a href="#" onClick={this.logout}>
              <Icon name="sign-out"/>
              Выйти
            </a>
          </ButtonGroup>
        </div>
      </div>
    )`

module.exports = Header
