`/** @jsx React.DOM */`

React = require "react"
$ = require "jquery"
require "moment"
require "md5"

Ctx = require "Engage360d/services/Context"

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

User = React.createClass
  getDefaultProps: ->
    page: null

  getInitialState: ->
    user: {}
    active: false

  componentWillMount: ->
    Ctx.get("router").on "change", (result) =>
      if result[0].handler is "users.edit"
        id = result[0].params.id
        if id > 0
          Ctx.get("ajax").get "/api/users/#{id}", (user) =>
            @setState active: true, user: user
        else
          @setState active: true, user: {}
      else
        @setState active: false

  createChangeHandler: (field) ->
    (event) =>
      user = @state.user
      user[field] = event.target.value
      @setState user: user

  onSave: ->
    user = @state.user
    if user.id
      data =
        firstname: user.firstname
        lastname: user.lastname

      Ctx.get("ajax").put "/api/users/#{user.id}", data, (user) =>
        Ctx.get("router").handle "#!/users"
    else
      data = user
      data.birthday = moment(user.birthday, "DD.MM.YYYY").format "YYYY-MM-DD"

      password = window.md5()
      data.plainPassword =
        first: password
        second: password

      Ctx.get("ajax").post "/api/users", data, (user) =>
        Ctx.get("router").handle "#!/users"

  onCancel: ->
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
                      <Input value={this.state.user.firstname} onChange={this.createChangeHandler("firstname")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Email</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <Input value={this.state.user.email} onChange={this.createChangeHandler("email")}/>
                    </Column>
                  </Field>
                  <Field>
                    <Column mods={["Size3"]}>
                      <Label>Дата рождения</Label>
                    </Column>
                    <Column mods={["Size6"]}>
                      <DateInput value={this.state.user.birthday} onChange={this.createChangeHandler("birthday")}/>
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
                      <Input value={this.state.user.facebookId} onChange={this.createChangeHandler("facebookId")}/>
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

module.exports = User
