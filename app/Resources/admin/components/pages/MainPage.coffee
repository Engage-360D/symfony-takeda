`/** @jsx React.DOM */`

React = require "React"

Auth = require "./../services/Auth"
Page = require "./layout/page/Page"
PageContainer = require "./layout/page/PageContainer"
Header = require "./layout/header/Header"
Navigation = require "./layout/navigation/Navigation"
Icon = require "./../components/icon/Icon"

Pages = require "./layout/container/pages/Pages"

MainPage = React.createClass
  getInitialState: ->
    users: []
    currentPage: null

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

  createPageChangeHandler: (page) ->
    =>
      @setState currentPage: `(<Pages/>)`

  render: ->
    `(
      <div>
        <Page>
          <Header/>
          <div className="PageBody">
            <Navigation title="Навигация">
              <a href="#" onClick={this.createPageChangeHandler("pages")}>
                <Icon name="home"/>
                Страницы
              </a>
            </Navigation>
            <PageContainer>
              {this.state.currentPage}
            </PageContainer>
          </div>
        </Page>
      </div>
    )`

module.exports = MainPage
