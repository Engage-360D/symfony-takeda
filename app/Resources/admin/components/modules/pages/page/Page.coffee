`/** @jsx React.DOM */`

React = require "react"
$ = require "jquery"

Ctx = require "Engage360d/services/Context"
LinkModelMixin = require "Engage360d/mixins/LinkModelMixin"
Editor = require "Engage360d/components/editor/Editor"
Input = require "Engage360d/components/form/field/Input"
Field = require "Engage360d/components/form/Field"
Label = require "Engage360d/components/form/Label"
Tab = require "Engage360d/components/tabs/Tab"
TabPanel = require "Engage360d/components/tabs/TabPanel"
Icon = require "Engage360d/components/icon/Icon"
Panel = require "Engage360d/components/panel/Panel"
PanelHeader = require "Engage360d/components/panel/PanelHeader"
PanelBody = require "Engage360d/components/panel/PanelBody"
PanelFooter = require "Engage360d/components/panel/PanelFooter"
PageHeader = require "Engage360d/components/page/PageHeader"
PageContent = require "Engage360d/components/page/PageContent"
Container = require "Engage360d/components/container/Container"
Column = require "Engage360d/components/column/Column"
Button = require "Engage360d/components/button/Button"
PageModel = require "Engage360d/modules/pages/model/Page"

Page = React.createClass
  mixins: [LinkModelMixin]

  getDefaultProps: ->
    page: null

  getInitialState: ->
    page: {}
    category: null
    active: false

  componentWillMount: ->
    Ctx.get("router").on "change", (result) =>
      if result[0].handler is "categories.pages.edit"
        id = result[0].params.pageId
        category = result[0].params.id
        if id > 0
          @model = new PageModel id: id
          @model.fetch
            success: =>
              @setState active: true
        else
          @model = new PageModel category: category
          @setState active: true
      else
        @setState active: false

  onSave: ->
    @model.save null, success: @onCancel

  onCancel: ->
    Ctx.get("router").handle "#!/categories/#{@model.getCategoryId()}"

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
            <PanelHeader title="Редактирование страницы"></PanelHeader>
            <PanelBody>
              <TabPanel>
                <Tab title="Общая информация">
                  <Container/>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Страница категории</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input
                        type="checkbox"
                        valueLink={this.linkModel("main")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Заголовок</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input
                        valueLink={this.linkModel("title")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Контент</Label>
                    </Column>
                    <Editor
                      valueLink={this.linkModel("blocks")}/>
                  </Field>
                </Tab>
                <Tab title="SEO">
                  <Container/>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Title</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input
                        valueLink={this.linkModel("seoTitle")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Keywords</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input
                        valueLink={this.linkModel("seoKeywords")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Description</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input
                        valueLink={this.linkModel("seoDescription")}/>
                    </Column>
                  </Field>
                </Tab>
                <Tab title="URL и переадресация">
                  <Container/>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>URL</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input
                        valueLink={this.linkModel("url")}/>
                    </Column>
                  </Field>
                </Tab>
              </TabPanel>
            </PanelBody>
            <PanelFooter>
              <Column mods={["Size3"]}></Column>
              <Column mods={["Size6"]}>
                <Button mods="Primary" onClick={this.onSave}>Сохранить</Button>
                <Button onClick={this.onCancel}>Отмена</Button>
              </Column>
            </PanelFooter>
          </Panel>
        </PageContent>
      </div>
    )`

module.exports = Page
