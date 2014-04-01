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

Category = React.createClass
  getDefaultProps: ->
    title: "Новый раздел"

  getInitialState: ->
    active: false
    category: {}
    title: @props.title

  componentWillMount: ->
    Ctx.get("router").on "change", (result) =>
      if result[0].handler is "categories.edit"
        id = result[0].params.id
        if id > 0
          $.oajax
            url: "/api/categories/#{id}"
            jso_provider: "engage360d"
            jso_allowia: true
            dataType: "json"
            success: (category) =>
              @setState active: true, category: category
        else
          @setState active: true, category: {}
      else
        @setState active: false

  componentDidUpdate: (props, state) ->
    if state.category isnt @state.category
      @setState title: @state.category.name or @props.title

  createOnChangeHandler: (field) ->
    (event) =>
      category = @state.category
      category[field] = event.target.value
      @setState category: category

  onSave: ->
    category = @state.category
    if category.id
      action = "/api/categories/#{category.id}"
      method = "PUT"
      data =
        name: category.name
        url: category.url
    else
      action = "/api/categories"
      method = "POST"
      data = category

    $.oajax
      url: action
      type: method
      jso_provider: "engage360d"
      jso_allowia: true
      dataType: "json"
      data: data
      success: (category) =>
        Ctx.get("eventBus").emit "category.update", category
        Ctx.get("router").handle "#!/categories/#{category.id}"

  onCancel: ->
    Ctx.get("router").back()

  onRemove: ->

  render: ->
    return `(<div/>)` unless @state.active

    `(
      <div>
        <PageHeader>
          <Icon name="file-text"/>
          Добавление / Редактирование раздела
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
                  <Input value={this.state.category.name} onChange={this.createOnChangeHandler("name")}/>
                </Column>
              </Field>
              <Field>
                <Column mods={["Size3"]}>
                  <Label>URL</Label>
                </Column>
                <Column mods={["Size6"]}>
                  <Input value={this.state.category.slug} onChange={this.createOnChangeHandler("url")}/>
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

module.exports = Category
