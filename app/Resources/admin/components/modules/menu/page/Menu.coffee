`/** @jsx React.DOM */`

React = require "React"

Ctx = require "Engage360d/services/Context"
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

Menu = React.createClass
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
          Ctx.get("ajax").get "/api/menus/#{id}", (menu) =>
            @setState active: true, menu: menu
        else
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
    menu = @state.menu
    if menu.id
      data =
        name: menu.name
        url: menu.url
      Ctx.get("ajax").put "/api/menus/#{menu.id}", data, (menu) =>
        Ctx.get("eventBus").emit "menu.update", menu
        Ctx.get("router").handle "#!/menu/#{menu.id}"
    else
      Ctx.get("ajax").post "/api/menus", menu, (menu) =>
        Ctx.get("eventBus").emit "menu.update", menu
        Ctx.get("router").handle "#!/menu/#{menu.id}"

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
                  <Input value={this.state.menu.name} onChange={this.createOnChangeHandler("name")}/>
                </Column>
              </Field>
              <Field>
                <Column mods={["Size3"]}>
                  <Label>Ссылка</Label>
                </Column>
                <Column mods={["Size6"]}>
                  <Input value={this.state.menu.slug} onChange={this.createOnChangeHandler("url")}/>
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
