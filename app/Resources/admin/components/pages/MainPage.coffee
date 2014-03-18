`/** @jsx React.DOM */`

React = require "React"

Auth = require "./../services/Auth"
Page = require "./layout/page/Page"
Header = require "./layout/header/Header"
Navigation = require "./layout/navigation/Navigation"
Icon = require "./../components/icon/Icon"

MainPage = React.createClass
  getInitialState: ->
    users: []

  componentWillMount: ->
    $.oajax
      url: "/api/users"
      jso_provider: "engage360d",
      jso_allowia: true
      dataType: "json"
      success: (data) =>
        @setState users: data

  logout: ->
    Auth.logout()

  render: ->
    `(
      <div>
        <Page>
          <Header/>
          <Navigation title="Навигация">
            <a href="#">
              <Icon name="home"/>
              Главная
            </a>
          </Navigation>
        </Page>
      </div>
    )`

module.exports = MainPage
