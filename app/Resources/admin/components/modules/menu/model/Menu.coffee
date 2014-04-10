Model = require "Engage360d/data/Model"
require "moment"


Menu = Model.extend
  urlRoot: "/api/menus"

  fields:
    id:
      persist: false
    children:
      persist: false
    level:
      persist: false
    path:
      persist: false
    slug:
      persist: false
    parent:
      converter: (value) ->
        value and value.id

module.exports = Menu
