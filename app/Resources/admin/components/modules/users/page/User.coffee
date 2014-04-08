`/** @jsx React.DOM */`

React = require "react"
$ = require "jquery"
require "moment"
require "md5"

Ctx = require "Engage360d/services/Context"
LinkModelMixin = require "Engage360d/mixins/LinkModelMixin"
Editor = require "Engage360d/components/editor/Editor"
Input = require "Engage360d/components/form/field/Input"
DateInput = require "Engage360d/components/form/field/DateInput"
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
UserModel = require "Engage360d/modules/users/model/User"

User = React.createClass
  mixins: [LinkModelMixin]

  getInitialState: ->
    active: false

  componentWillMount: ->
    Ctx.get("router").on "change", (result) =>
      if result[0].handler is "users.edit"
        id = result[0].params.id
        if id > 0
          @model = new UserModel id: id
          @model.fetch
            success: =>
              @setState active: true
        else
          @model = new UserModel
            plainPassword: @generatePassword()
          @setState active: true
      else
        @setState active: false

  generatePassword: ->
    password = window.md5()
    first: password
    second: password

  handleSave: ->
    @model.save null, success: =>
      Ctx.get("router").handle "#!/users"

  handleCancel: ->
    Ctx.get("router").handle "#!/users"

  render: ->
    return `(<div/>)` unless @state.active

    `(
      <div>
        <PageHeader>
          <Icon name="file-text"/>
          Пользователи
        </PageHeader>
        <PageContent>
          <Panel>
            <PanelHeader title="Редактирование пользователя"></PanelHeader>
            <PanelBody>
              <TabPanel>
                <Tab title="Общая информация">
                  <Container/>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Имя</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input
                        valueLink={this.linkModel("firstname")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Email</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input
                        valueLink={this.linkModel("email")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Дата рождения</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <DateInput
                        valueLink={this.linkModel("birthday")}/>
                    </Column>
                  </Field>
                </Tab>
                <Tab title="Соц. сети">
                  <Container/>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Facebook ID</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input
                        valueLink={this.linkModel("facebookId")}/>
                    </Column>
                  </Field>
                </Tab>
              </TabPanel>
            </PanelBody>
            <PanelFooter>
              <Column mods={["Size3"]}></Column>
              <Column mods={["Size6"]}>
                <Button mods="Primary" onClick={this.handleSave}>Сохранить</Button>
                <Button onClick={this.handleCancel}>Отмена</Button>
              </Column>
            </PanelFooter>
          </Panel>
        </PageContent>
      </div>
    )`

module.exports = User
