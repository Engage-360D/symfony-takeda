`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"
LinkModelMixin = require "Engage360d/mixins/LinkModelMixin"
Input = require "Engage360d/components/form/field/Input"
Field = require "Engage360d/components/form/Field"
Label = require "Engage360d/components/form/Label"
Icon = require "Engage360d/components/icon/Icon"
Panel = require "Engage360d/components/panel/Panel"
PanelHeader = require "Engage360d/components/panel/PanelHeader"
PanelBody = require "Engage360d/components/panel/PanelBody"
PanelFooter = require "Engage360d/components/panel/PanelFooter"
Column = require "Engage360d/components/column/Column"
Button = require "Engage360d/components/button/Button"
PageHeader = require "Engage360d/components/page/PageHeader"
PageContent = require "Engage360d/components/page/PageContent"
MenuModel = require "Engage360d/modules/menu/model/Menu"

Menu = React.createClass
  mixins: [LinkModelMixin]

  getDefaultProps: ->
    title: "Новое меню"

  getInitialState: ->
    active: false
    menu: {}
    title: @props.title

  componentWillMount: ->
    Ctx.get("router").on "change", (result) =>
      if result[0].handler is "menu.edit"
        id = result[0].params.id
        if id > 0
          @model = new MenuModel id: id
          @model.fetch
            success: =>
              @setState active: true
        else
          @model = new MenuModel
          @setState active: true, menu: {}
      else
        @setState active: false

  componentDidUpdate: (props, state) ->
    if state.menu isnt @state.menu
      @setState name: @state.menu.name or @props.title

  createOnChangeHandler: (field) ->
    (event) =>
      menu = @state.menu
      menu[field] = event.target.value
      @setState menu: menu

  onSave: ->
    @model.save null, success: =>
      Ctx.get("eventBus").emit "menu.update", @model
      Ctx.get("router").handle "#!/menu/#{@model.get "id"}"

  onCancel: ->
    Ctx.get("router").back()

  onRemove: ->

  render: ->
    return `(<div/>)` unless @state.active

    `(
      <div>
        <PageHeader>
          <Icon name="file-text"/>
          Добавление / Редактирование меню
        </PageHeader>
        <PageContent>
          <Panel>
            <PanelHeader title={this.state.title}></PanelHeader>
            <PanelBody>
              <Field>
                <Column mods={["Size3"]}>
                  <Label>Название</Label>
                </Column>
                <Column mods={["Size6"]}>
                  <Input
                    valueLink={this.linkModel("name")}/>
                </Column>
              </Field>
              <Field>
                <Column mods={["Size3"]}>
                  <Label>Ссылка</Label>
                </Column>
                <Column mods={["Size6"]}>
                  <Input
                    valueLink={this.linkModel("url")}/>
                </Column>
              </Field>
            </PanelBody>
            <PanelFooter>
              <Column mods={["Size3"]}></Column>
              <Column mods={["Size6"]}>
                <Button mods="Primary" onClick={this.onSave}>Сохранить</Button>
                <Button onClick={this.onCancel}>Отменить</Button>
                <Button mods="Primary" onClick={this.onRemove}>Удалить</Button>
              </Column>
            </PanelFooter>
          </Panel>
        </PageContent>
      </div>
    )`

module.exports = Menu
