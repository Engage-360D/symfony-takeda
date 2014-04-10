
module.exports = ->
  React = require "react"

  Auth = require "Engage360d/services/Auth"
  Ctx = require "Engage360d/services/Context"
  
  Ctx.register("eventBus").object require "Engage360d/services/EventBus"
  Ctx.register("router").object require "Engage360d/services/Router"
  Ctx.register("auth").object new Auth("1_21g8rd7kv50ggw4wk8gwogock4c44c8sgoc4c4000o0kcsw0w4")
  Ctx.register("ajax").object require "Engage360d/services/Ajax"
  
  Ctx.register "navigation", Array, [
    require("Engage360d/modules/pages/navigation/PagesNavigation")()
    require("Engage360d/modules/users/navigation/UsersNavigation")()
    require("Engage360d/modules/menu/navigation/MenuNavigation")()
  ]
  
  Ctx.register "pages", Array, [
    require("Engage360d/modules/pages/page/Category")()
    require("Engage360d/modules/pages/page/Pages")()
    require("Engage360d/modules/pages/page/Page")()
    require("Engage360d/modules/menu/page/Menu")()
    require("Engage360d/modules/menu/page/Menus")()
    require("Engage360d/modules/menu/page/Node")()
    require("Engage360d/modules/users/page/Users")()
    require("Engage360d/modules/users/page/User")()
  ]
  
  Ctx.initialize()
  
  LoginPage = require "LoginPage"
  MainPage = require "MainPage"

  if Ctx.get("auth").isAuthorized()
    React.renderComponent MainPage(), document.getElementsByTagName("body")[0]
  else
    React.renderComponent LoginPage(), document.getElementsByTagName("body")[0]