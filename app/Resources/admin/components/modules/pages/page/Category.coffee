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
CategoryModel = require "Engage360d/modules/pages/model/Category"

Category = React.createClass
  mixins: [LinkModelMixin]

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
          @model = new CategoryModel id: id
          @model.fetch
            success: =>
              @setState active: true
        else
          @model = new CategoryModel
          @setState active: true, category: {}
      else
        @setState active: false

  componentDidUpdate: (props, state) ->
    if state.category isnt @state.category
      @setState title: @state.category.name or @props.title

  onSave: ->
    @model.save null, success: =>
      Ctx.get("eventBus").emit "category.update", @model
      Ctx.get("router").handle "#!/categories/#{@model.get "id"}"

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
                  <Input
                    value={this.state.category.name}
                    valueLink={this.linkModel("name")}/>
                </Column>
              </Field>
              <Field>
                <Column mods={["Size3"]}>
                  <Label>URL</Label>
                </Column>
                <Column mods={["Size6"]}>
                  <Input
                    value={this.state.category.slug}
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

module.exports = Category
