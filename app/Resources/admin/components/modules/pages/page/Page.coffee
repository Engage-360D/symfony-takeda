`/** @jsx React.DOM */`

React = require "react"
$ = require "jquery"

Ctx = require "Engage360d/services/Context"

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

Page = React.createClass
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
        if id > 0
          Ctx.get("ajax").get "/api/pages/#{id}", (page) =>
            @setState active: true, category: result[0].params.id, page: page
        else
          @setState active: true, category: result[0].params.id, page: {}
      else
        @setState active: false

  onChangeBlocks: (data) ->
    page = @state.page
    page.blocks = data
    @setState page: page

  onChangeTitle: (event) ->
    page = @state.page
    page.title = event.target.value
    @setState page: page

  createChangeHandler: (field) ->
    (event) =>
      page = @state.page
      page[field] = event.target.value
      @setState page: page

  onSave: ->
    page = $.extend @state.page, category: @state.category
    if page.id
      delete page.slug
      Ctx.get("ajax").put "/api/pages/#{page.id}", page, (response) =>
        Ctx.get("router").handle "#!/categories/#{@state.category}"
    else
      Ctx.get("ajax").post "/api/pages", page, (response) =>
        Ctx.get("router").handle "#!/categories/#{@state.category}"

  onCancel: ->
    Ctx.get("router").handle "#!/categories/#{@state.category}"

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
                      <Input type="checkbox" value={this.state.page.main} onChange={this.createChangeHandler("main")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Заголовок</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input value={this.state.page.title} onChange={this.createChangeHandler("title")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Контент</Label>
                    </Column>
                    <Editor blocks={this.state.page.blocks} onChange={this.onChangeBlocks}/>
                  </Field>
                </Tab>
                <Tab title="SEO">
                  <Container/>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Title</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input value={this.state.page.seoTitle} onChange={this.createChangeHandler("seoTitle")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Keywords</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input value={this.state.page.seoKeywords} onChange={this.createChangeHandler("seoKeywords")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Description</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input value={this.state.page.seoDescription} onChange={this.createChangeHandler("seoDescription")}/>
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
                      <Input value={this.state.page.url} onChange={this.createChangeHandler("url")}/>
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
