`/** @jsx React.DOM */`

React = require "react"

Ctx = require "Engage360d/services/Context"

NavigationBlock = require "Engage360d/components/navigation/NavigationBlock"
MenuCollection = require "Engage360d/modules/menu/model/MenuCollection"

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
      @menus.add [menu], merge: true
      @setState menu: @menus.toRawJSON(), links: @extractLinks @menus.toRawJSON()

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
    @menus = new MenuCollection()
    @menus.fetch
      success: (menus) =>
        @setState menu: menus.toRawJSON(), links: @extractLinks menus.toRawJSON()

  removeMenu: (id) ->
    menu = @menus.get id
    return alert "Невозможно удалить. Содержит дочерние элементы." if menu.get("children").length > 0
    return unless confirm "Вы уверены что хотите удалить?"
    menu.destroy
      success: =>
        @setState menu: @menus.toRawJSON(), links: @extractLinks @menus.toRawJSON()
        Ctx.get("router").back()

  render: ->
    `(
      <NavigationBlock
        active={this.state.active}
        title="Меню"
        icon="file-text"
        links={this.state.links}/>
    )`

module.exports = MenuNavigation
