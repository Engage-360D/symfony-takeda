`/** @jsx React.DOM */`

React = require "React"

Ctx = require "Engage360d/services/Context"

NavigationBlock = require "Engage360d/components/navigation/NavigationBlock"

PagesNavigation = React.createClass
  getDefaultProps: ->
    links: [
      title: "Добавить раздел"
      url: "#!/categories/new/edit"
      icon: "plus"
    ]

  getInitialState: ->
    active: false
    categories: []
    links: @props.links

  componentWillMount: ->
    Ctx.get("router").add [{ path: "/categories/:id/edit", handler: "categories.edit" }]
    Ctx.get("router").add [{ path: "/categories/:id", handler: "categories" }]
    Ctx.get("router").add [{ path: "/categories/:id/pages/:pageId/edit", handler: "categories.pages.edit" }]

    Ctx.get("eventBus").on "category.update", (category) =>
      # move to mixin AddOrReplace
      categories = @state.categories
      exists = false
      for cat, index in categories
        if cat.id is category.id
          exists = true
          categories[index] = category
      categories.push category unless exists

      @setState categories: categories, links: @extractLinks categories

    Ctx.get("router").on "change", (result) =>
      @setState
        active: result[0].handler.indexOf("categories") isnt -1
        links: @extractLinks @state.categories

    @loadCategories()

  extractLinks: (categories, active) ->
    state = Ctx.get("router").state()
    links = categories.map (category) =>
      id: category.id
      title: category.name
      url: "#!/categories/#{category.id}"
      active: state is "#!/categories/#{category.id}"
      actions: [{
        icon: "edit"
        url: "#!/categories/#{category.id}/edit"
      },{
        icon: "trash-o"
        onClick: @removeCategory
      }]

    links.concat @props.links

  loadCategories: ->
    Ctx.get("ajax").get "/api/categories", (data) =>
      @setState categories: data, links: @extractLinks data

  removeCategory: (category) ->
    return unless confirm "Вы уверены что хотите удалить?"
    Ctx.get("ajax").get "/api/categories/#{category}/pages", (pages) =>
      return alert "Невозможно удалить. Содержит дочерние элементы." if pages.length > 0
      Ctx.get("ajax").remove "/api/categories/#{category}", =>
        updated = []
        for cat in @state.categories
          updated.push cat unless cat.id is category
        @setState categories: updated, links: @extractLinks updated

  render: ->
    `(
      <NavigationBlock
        active={this.state.active}
        title="Страницы"
        icon="file-text"
        links={this.state.links}/>
    )`

module.exports = PagesNavigation
