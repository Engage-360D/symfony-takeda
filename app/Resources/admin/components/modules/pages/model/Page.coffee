Model = require "Engage360d/data/Model"


Page = Model.extend
  urlRoot: "/api/pages"

  fields:
    id:
      persist: false
    slug:
      persist: false
    category:
      converter: (value) ->
        value.id

  getCategoryId: ->
    if @get("category") > 0 then @get("category") else @get("category").id

module.exports = Page
