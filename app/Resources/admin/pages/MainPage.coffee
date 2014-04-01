`/** @jsx React.DOM */`

React = require "React"
$ = require "jquery"

Ctx = require "Engage360d/services/Context"
Page = require "Engage360d/components/page/Page"
PageContainer = require "Engage360d/components/page/PageContainer"
Header = require "Engage360d/components/header/Header"
Navigation = require "Engage360d/components/navigation/Navigation"
Icon = require "Engage360d/components/icon/Icon"

MainPage = React.createClass
  getInitialState: ->
    height: $(document).height()

  componentDidMount: ->
    Ctx.get("router").recognize()

  render: ->
    `(
      <div>
        <Page>
          <Header/>
          <div className="PageBody" style={{"height": this.state.height}}>
            <Navigation title="Навигация">
              {Ctx.get("navigation")}
            </Navigation>
            <PageContainer>
              {Ctx.get("pages")}
            </PageContainer>
          </div>
        </Page>
      </div>
    )`

module.exports = MainPage
