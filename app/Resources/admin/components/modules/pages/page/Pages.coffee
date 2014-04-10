`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"

Grid = require "Engage360d/components/grid/Grid"
Button = require "Engage360d/components/button/Button"
Icon = require "Engage360d/components/icon/Icon"
Select = require "Engage360d/components/form/field/Select"
Column = require "Engage360d/components/column/Column"
Container = require "Engage360d/components/container/Container"
Panel = require "Engage360d/components/panel/Panel"
PanelHeader = require "Engage360d/components/panel/PanelHeader"
PanelBody = require "Engage360d/components/panel/PanelBody"
PanelFooter = require "Engage360d/components/panel/PanelFooter"
PageHeader = require "Engage360d/components/page/PageHeader"
PageContent = require "Engage360d/components/page/PageContent"
PageCollection = require "Engage360d/modules/pages/model/PageCollection"
Page = require "Engage360d/modules/pages/model/Page"

Pages = React.createClass
  getDefaultProps: ->
    columns: [
      {name: "id", title: "ID", width: 50},
      {name: "title", title: "Заголовок"},
      {name: "url", title: "URL"}
    ]
    actions: [
      {name: "edit", icon: "pencil"},
      {name: "delete", icon: "trash-o"}
    ]
    pages: [
      {text: 10, value: 10},
      {text: 25, value: 25},
      {text: 50, value: 50},
      {text: 100, value: 100}
    ]

  getInitialState: ->
    category: null
    pages: []
    active: false
    pageCount: 10

  componentWillMount: ->
    Ctx.get("router").on "change", (result) =>
      unless result[0].handler is "categories"
        return @setState active: false

      category = result[0].params.id
      @setState category: category, active: true

      @pages = new PageCollection 
      @pages.fetch
        url: "/api/categories/#{category}/pages"
        success: (pages) =>
          @setState pages: pages.toRawJSON()

  removePage: (id) ->
    @pages.get(id).destroy
      success: =>
        @setState pages: @pages.toRawJSON()

  onHandleAction: (action, row) ->
    if action is "edit"
      Ctx.get("router").handle "#!/categories/#{@state.category}/pages/#{row.id}/edit"
    if action is "delete"
      return unless confirm "Вы уверены что хотите удалить?"
      @removePage row.id

  handleCreatePage: ->
    Ctx.get("router").handle "#!/categories/#{@state.category}/pages/new/edit"

  render: ->
    return `(<div/>)` unless @state.active

    `(
      <div>
        <PageHeader>
          <Icon name="file-text"/>
          Страницы
        </PageHeader>
        <PageContent>
          <Panel>
            <PanelHeader title="Список статических страниц"></PanelHeader>
            <PanelBody>
              <Container>
                <Container padding="0 0 20 0">
                  <Column mods="Size2">
                    <Button mods="Primary" onClick={this.handleCreatePage}>Создать</Button>
                  </Column>
                  <Column mods="Size2" style={{"text-align": "right"}}>
                    <Container padding="5 0 0 0" inline={true} style={{"vertical-align": "middle", "text-align": "right"}}>
                      Записей на страницу
                    </Container>
                  </Column>
                  <Column mods="Size1">
                    <Select options={this.props.pages} value={this.state.pageCount} style={{top: "16px"}}/>
                  </Column>
                </Container>
                <Grid
                  columns={this.props.columns}
                  actions={this.props.actions}
                  items={this.state.pages}
                  onHandleAction={this.onHandleAction}/>
                <Container padding="20 0 0 0">
                  <Button mods="Primary" onClick={this.handleCreatePage}>Создать</Button>
                </Container>
              </Container>
            </PanelBody>
          </Panel>
        </PageContent>
      </div>
    )`

module.exports = Pages
