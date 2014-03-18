`/** @jsx React.DOM */`

React = require "React"

Icon = require "./../../../components/icon/Icon"
Logo = require "./../../../components/logo/Logo"
Button = require "./../../../components/button/Button"
ButtonGroup = require "./../../../components/button/ButtonGroup"

Auth = require "./../../../services/Auth"

Header = React.createClass
  getInitialState: ->
    user: null

  componentWillMount: ->
    $.oajax
      url: "/api/me"
      jso_provider: "engage360d",
      jso_allowia: true
      dataType: "json"
      success: (data) =>
        @setState user: data

  getUserData: ->
    return unless @state.user
    [@state.user.firstname, @state.user.lastname].join(" ")

  render: ->
    `(
      <div className="Header">
        <Logo/>
        <Button mods={["Header", "MenuToggle"]}>
          <Icon name="bars"/>
        </Button>
        <div className="HeaderNav">
          <ButtonGroup button={this.getUserData()} mods={["Header", "Right"]}>
            <a href="#" onClick={Auth.logout}>
              <Icon name="sign-out"/>
              Выйти
            </a>
          </ButtonGroup>
        </div>
      </div>
    )`

module.exports = Header
