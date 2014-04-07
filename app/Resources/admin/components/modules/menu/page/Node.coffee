`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"

Input = require "Engage360d/components/form/field/Input"
Field = require "Engage360d/components/form/Field"
Label = require "Engage360d/components/form/Label"
Icon = require "Engage360d/components/icon/Icon"
Panel = require "Engage360d/components/panel/Panel"
Modal = require "Engage360d/components/modal/Modal"
PanelHeader = require "Engage360d/components/panel/PanelHeader"
PanelBody = require "Engage360d/components/panel/PanelBody"
PanelFooter = require "Engage360d/components/panel/PanelFooter"
Column = require "Engage360d/components/column/Column"
Button = require "Engage360d/components/button/Button"
PageHeader = require "Engage360d/components/page/PageHeader"
PageContent = require "Engage360d/components/page/PageContent"

Node = React.createClass
  getDefaultProps: ->
    title: "Новое меню"

  getInitialState: ->
    active: false
    parent: null
    node: {}
    urls: []

  componentWillMount: ->
    Ctx.get("router").on "change", (result) =>
      if result[0].handler is "menu.node.edit"
        params = result[0].params
        parent = params.id
        if params.nodeId is "new"
          @setState active: true, parent: parent
        else
          Ctx.get("ajax").get "/api/menus/#{params.nodeId}", (node) =>
            @setState active: true, node: node
      else
        @setState active: false

  createChangeHandler: (field) ->
    (event) =>
      node = @state.node
      node[field] = event.target.value
      @setState node: node

  onSave: ->
    node = @state.node
    if node.id
      data =
        name: node.name
        url: node.url
        parent: node.parent.id
      Ctx.get("ajax").put "/api/menus/#{node.id}", data, (menu) =>
        @setState node: {}
        Ctx.get("router").back()
    else
      data =
        name: node.name
        url: node.url
        parent: @state.parent
      Ctx.get("ajax").post "/api/menus", data, (menu) =>
        @setState node: {}
        Ctx.get("router").back()

  onCancel: ->
    Ctx.get("router").back()

  onClose: ->
    Ctx.get("router").back()

  filterUrls: (term) ->
    @state.urls.filter (url) ->
      url.url.indexOf(term) isnt -1

  urlSource: (request, response) ->
    Ctx.get("ajax").get "/api/urls/#{request.term}", (data) =>
      urls = []
      for url in data
        urls.push
          url: url.url
          type: if url.type is "category" then "Категория" else "Страница"
      @setState urls: urls
      response @filterUrls request.term

  onSelectUrl: (event, ui) ->
    event.preventDefault()
    node = @state.node
    node.url = ui.item.url
    @setState node: node

  renderAutocomplete: (ul, item) ->
    $("<li>")
      .append("<a>" + item.url + "<span>" + item.type + "</span></a>")
      .appendTo(ul)

  render: ->
    return `(<div/>)` unless @state.active

    `(
      <Modal show={true} title="Редактирование пункта меню" onClose={this.onClose}>
        <Panel>
          <PanelBody>
            <Field>
              <Column mods={["Size3"]}>
                <Label>Название</Label>
              </Column>
              <Column mods={["Size6"]}>
                <Input value={this.state.node.name} onChange={this.createChangeHandler("name")}/>
              </Column>
            </Field>
            <Field>
              <Column mods={["Size3"]}>
                <Label>Ссылка</Label>
              </Column>
              <Column mods={["Size6"]}>
                <Input
                  value={this.state.node.url}
                  onChange={this.createChangeHandler("url")}
                  autocomplete={{select: this.onSelectUrl,source: this.urlSource, render: this.renderAutocomplete}}
                  />
              </Column>
            </Field>
          </PanelBody>
          <PanelFooter>
            <Column mods={["Size3"]}></Column>
            <Column mods={["Size6"]}>
              <Button mods="Primary" onClick={this.onSave}>Сохранить</Button>
              <Button onClick={this.onCancel}>Отменить</Button>
            </Column>
          </PanelFooter>
        </Panel>
      </Modal>
    )`

module.exports = Node
