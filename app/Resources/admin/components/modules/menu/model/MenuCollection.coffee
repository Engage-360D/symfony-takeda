Collection = require "Engage360d/data/Collection"
MenuModel = require "Engage360d/modules/menu/model/Menu"


MenuCollection = Collection.extend
  url: "/api/menus"
  model: MenuModel


module.exports = MenuCollection
