Collection = require "Engage360d/data/Collection"
UserModel = require "Engage360d/modules/users/model/User"


UserCollection = Collection.extend
  url: "/api/users"
  model: UserModel


module.exports = UserCollection
