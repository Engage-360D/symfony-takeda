Model = require "Engage360d/data/Model"


Category = Model.extend
  urlRoot: "/api/categories"

  fields:
    id:
      persist: false
    page:
      persist: false
    pages:
      persist: false
    slug:
      persist: false


module.exports = Category
