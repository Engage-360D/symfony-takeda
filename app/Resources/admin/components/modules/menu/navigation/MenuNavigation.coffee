`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"

NavigationBlock = require "Engage360d/components/navigation/NavigationBlock"

MenuNavigation = React.createClass
  getDefaultProps: ->
    links: [{
      title: "Добавить меню"
      url: "#!/menu/new/edit"
      icon: "plus"
    }]

  getInitialState: ->
    active: false
    menu: []
    links: @props.links

  componentWillMount: ->
    Ctx.get("router").add [{ path: "/menu/:id/edit", handler: "menu.edit" }]
    Ctx.get("router").add [{ path: "/menu/:id", handler: "menu" }]
    Ctx.get("router").add [{ path: "/menu/:id/node/:nodeId", handler: "menu.node.edit" }]

    Ctx.get("eventBus").on "menu.update", (menu) =>
      exists = false
      menus = @state.menu
      for item, index in menus
        if item.id is menu.id
          exists = true
          menus[index] = menu
      menus.push menu unless exists

      @setState menu: menus, links: @extractLinks menus

    Ctx.get("router").on "change", (result) =>
      @setState
        active: result[0].handler.indexOf("menu") isnt -1
        links: @extractLinks @state.menu

    @loadMenu()

  extractLinks: (menus) ->
    state = Ctx.get("router").state()
    links = menus.map (menu) =>
      id: menu.id
      title: menu.name
      url: "#!/menu/#{menu.id}"
      active: state is "#!/menu/#{menu.id}"
      actions: [{
        icon: "edit"
        url: "#!/menu/#{menu.id}/edit"
      },{
        icon: "trash-o"
        onClick: @removeMenu
      }]

    links.concat @props.links

  loadMenu: ->
    Ctx.get("ajax").get "/api/menus", (data) =>
      @setState menu: data, links: @extractLinks data

  removeMenu: (id) ->
    canRemove = true
    for menu in @state.menu
      canRemove = false if id is menu.id and menu.children.length > 0

    return alert "Невозможно удалить. Содержит дочерние элементы." unless canRemove
    return unless confirm "Вы уверены что хотите удалить?"
    Ctx.get("ajax").remove "/api/menus/#{id}", =>
      updated = []
      for menu in @state.menu
        updated.push menu unless menu.id is id
      @setState menu: updated, links: @extractLinks updated

  render: ->
    `(
      <NavigationBlock
        active={this.state.active}
        title="Меню"
        icon="file-text"
        links={this.state.links}/>
    )`

module.exports = MenuNavigation
