`/** @jsx React.DOM */`

React = require "React"

Grid = require "./../../../../components/grid/Grid"
Button = require "./../../../../components/button/Button"
PageDetail = require "./PageDetail"

Pages = React.createClass
  getDefaultProps: ->
    columns: [
      {name: "title", title: "Заголовок"},
      {name: "slug", title: "Slug"}
    ]

  getInitialState: ->
    pages: []
    active: null

  componentWillMount: ->
    $.oajax
      url: "/api/pages"
      jso_provider: "engage360d"
      jso_allowia: true
      dataType: "json"
      success: (data) =>
        @setState pages: data

  selectChangeHandler: (row) ->
    @setState active: row

  handleCreatePage: ->
    @setState active: {}

  handleSavePage: ->
    page = @refs.detailPage.state.page
    if page.id
      action = "/api/pages/#{page.id}"
      method = "PUT"
      data =
        title: page.title
        blocks: page.blocks
    else
      action = "/api/pages"
      method = "POST"
      data = page

    $.oajax
      url: action
      type: method
      jso_provider: "engage360d"
      jso_allowia: true
      dataType: "json"
      data: data
      success: (response) =>
        pages = @state.pages
        if page.id
          for item, index in pages
            pages[index] = response if item.id is page.id
        else
          pages.push data
        @setState pages: pages, active: null

  handleCancelPage: ->
    @setState active: null

  renderActive: ->
    `(
      <div>
        <PageDetail
          page={this.state.active}
          ref="detailPage"
          onSave={this.handleSavePage}
          onCancel={this.handleCancelPage}/>
      </div>
    )`

  renderGrid: ->
    `(
      <div>
        <Grid columns={this.props.columns} items={this.state.pages} onSelect={this.selectChangeHandler}/>
        <Button mods="Primary" onClick={this.handleCreatePage}>Создать</Button>
      </div>
    )`

  render: ->
    if @state.active
      @renderActive()
    else
      @renderGrid()

module.exports = Pages
