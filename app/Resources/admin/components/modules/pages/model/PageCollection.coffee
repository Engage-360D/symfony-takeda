Collection = require "Engage360d/data/Collection"
PageModel = require "Engage360d/modules/pages/model/Page"


PageCollection = Collection.extend
  url: "/api/pages"
  model: PageModel


module.exports = PageCollection
