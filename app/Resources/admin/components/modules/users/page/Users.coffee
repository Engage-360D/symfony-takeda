`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"
Grid = require "Engage360d/components/grid/Grid"
Button = require "Engage360d/components/button/Button"
Icon = require "Engage360d/components/icon/Icon"
Select = require "Engage360d/components/form/field/Select"
Column = require "Engage360d/components/column/Column"
Container = require "Engage360d/components/container/Container"
Panel = require "Engage360d/components/panel/Panel"
PanelHeader = require "Engage360d/components/panel/PanelHeader"
PanelBody = require "Engage360d/components/panel/PanelBody"
PanelFooter = require "Engage360d/components/panel/PanelFooter"
PageHeader = require "Engage360d/components/page/PageHeader"
PageContent = require "Engage360d/components/page/PageContent"

Users = React.createClass
  getDefaultProps: ->
    columns: [
      {name: "id", title: "ID", width: 50},
      {name: "username", title: "Логин"},
      {name: "lastname", title: "Фамилия"},
      {name: "firstname", title: "Имя"}
    ]
    actions: [
      {name: "edit", icon: "pencil"},
      {name: "delete", icon: "trash-o"}
    ]
    pages: [
      {text: 10, value: 10},
      {text: 25, value: 25},
      {text: 50, value: 50},
      {text: 100, value: 100}
    ]

  getInitialState: ->
    users: []
    active: false
    pageCount: 10

  componentWillMount: ->
    Ctx.get("router").on "change", (result) =>
      unless result[0].handler is "users"
        return @setState active: false

      @setState active: true
          
      $.oajax
        url: "/api/users"
        jso_provider: "engage360d"
        jso_allowia: true
        dataType: "json"
        success: (users) =>
          @setState users: users

  removeUser: (id) ->
    $.oajax
      url: "/api/users/#{id}"
      method: "DELETE"
      jso_provider: "engage360d"
      jso_allowia: true
      dataType: "json"
      success: =>
        users = @state.users
        updated = []
        for user, index in users
          updated.push user unless user.id is id
        @setState users: updated

  onHandleAction: (action, row) ->
    if action is "edit"
      Ctx.get("router").handle "#!/users/#{row.id}/edit"
    if action is "delete"
      return unless confirm "Вы уверены что хотите удалить?"
      @removeUser row.id

  handleCreateUser: ->
    Ctx.get("router").handle "#!/users/new/edit"

  handleSaveUser: ->
    user = @refs.detailPage.state.user
    if user.id
      action = "/api/users/#{user.id}"
      method = "PUT"
      data = user
    else
      action = "/api/users"
      method = "POST"
      data = user

    $.oajax
      url: action
      type: method
      jso_provider: "engage360d"
      jso_allowia: true
      dataType: "json"
      data: data
      success: (response) =>
        users = @state.users
        if user.id
          for item, index in users
            users[index] = response if item.id is user.id
        else
          users.push data
        @setState users: users, active: null

  handleCancelUser: ->
    @setState active: null

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
            <PanelHeader title="Список пользователей"></PanelHeader>
            <PanelBody>
              <Container>
                <Container padding="0 0 20 0">
                  <Column mods="Size2">
                    <Button mods="Primary" onClick={this.handleCreateUser}>Создать</Button>
                  </Column>
                  <Column mods="Size2" style={{"text-align": "right"}}>
                    <Container padding="5 0 0 0" inline={true} style={{"vertical-align": "middle", "text-align": "right"}}>
                      Записей на страницу
                    </Container>
                  </Column>
                  <Column mods="Size1">
                    <Select options={this.props.users} value={this.state.pageCount} style={{top: "16px"}}/>
                  </Column>
                </Container>
                <Grid
                  columns={this.props.columns}
                  actions={this.props.actions}
                  items={this.state.users}
                  onHandleAction={this.onHandleAction}/>
                <Container padding="20 0 0 0">
                  <Button mods="Primary" onClick={this.handleCreateUser}>Создать</Button>
                </Container>
              </Container>
            </PanelBody>
          </Panel>
        </PageContent>
      </div>
    )`

module.exports = Users
