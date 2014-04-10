Collection = require "Engage360d/data/Collection"
CategoryModel = require "Engage360d/modules/pages/model/Category"


CategoryCollection = Collection.extend
  url: "/api/categories"
  model: CategoryModel


module.exports = CategoryCollection
