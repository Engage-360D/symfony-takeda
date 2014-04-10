`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"

Grid = require "Engage360d/components/grid/Grid"
Button = require "Engage360d/components/button/Button"
Icon = require "Engage360d/components/icon/Icon"
Select = require "Engage360d/components/form/field/Select"
Tree = require "Engage360d/components/tree/Tree"
Modal = require "Engage360d/components/modal/Modal"
Column = require "Engage360d/components/column/Column"
Container = require "Engage360d/components/container/Container"
Panel = require "Engage360d/components/panel/Panel"
PanelHeader = require "Engage360d/components/panel/PanelHeader"
PanelBody = require "Engage360d/components/panel/PanelBody"
PanelFooter = require "Engage360d/components/panel/PanelFooter"
PageHeader = require "Engage360d/components/page/PageHeader"
PageContent = require "Engage360d/components/page/PageContent"
MenuCollection = require "Engage360d/modules/menu/model/MenuCollection"

Menus = React.createClass
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

  getInitialState: ->
    menu: null
    children: {}
    active: false
    edit: false
    selected: false

  componentWillMount: ->
    Ctx.get("router").on "change", (result) =>
      unless result[0].handler is "menu" or result[0].handler is "menu.node.edit"
        return @setState active: false

      menu = result[0].params.id
      @setState menu: menu, active: true
      @loadMenu menu

  loadMenu: (root) ->
    @menus = new MenuCollection()
    @menus.fetch
      url: "/api/menus/#{root}/children"
      success: (menus) =>
        @setState children: @extractTree menus.toRawJSON()

  extractTree: (nodes) ->
    @extractNode nodes[0]

  extractNode: (node) ->
    children = []
    for child in node.children
      children.push @extractNode child

    id: node.id
    text: node.name
    state:
      opened: true
    children: children

  onHandleAction: (action, row) ->
    if action is "edit"
      Ctx.get("router").handle "#!/categories/#{@state.category}/pages/#{row.id}/edit"
    if action is "delete"
      return unless confirm "Вы уверены что хотите удалить?"
      @removePage row.id

  onSelectNode: (data) ->
    @setState selected: data.node

  handleAppendMenu: ->
    Ctx.get("router").handle "#!/menu/#{@state.selected.id}/node/new"

  handleEditMenu: ->
    Ctx.get("router").handle "#!/menu/#{@state.menu}/node/#{@state.selected.id}"

  handleRemoveMenu: ->
    return if @state.selected.children.length > 0
    @menus.get(@state.selected.id).destroy
      success: =>
        @loadMenu @state.menu

  render: ->
    return `(<div/>)` unless @state.active

    `(
      <div>
        <PageHeader>
          <Icon name="file-text"/>
          Меню
        </PageHeader>
        <PageContent>
          <Panel>
            <PanelBody>
              <Container>
                <Container padding="0 0 20 0">
                  <Button mods="Primary" onClick={this.handleAppendMenu} disabled={!this.state.selected}>Создать</Button>
                  <Button mods="Primary" onClick={this.handleEditMenu} disabled={!this.state.selected}>Изменить</Button>
                  <Button mods="Primary" onClick={this.handleRemoveMenu} disabled={!this.state.selected}>Удалить</Button>
                </Container>
                <Container style={{border: "1px solid #eee"}}>
                  <Tree rootNode={this.state.children} onSelect={this.onSelectNode}/>
                </Container>
              </Container>
            </PanelBody>
          </Panel>
        </PageContent>
      </div>
    )`

module.exports = Menus
