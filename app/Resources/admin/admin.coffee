
React = require "React"
Auth = require "./components/services/Auth"
LoginPage = require "./components/pages/login/LoginPage"
MainPage = require "./components/pages/MainPage"

init = ->
  if Auth.isAuthorized()
    React.renderComponent MainPage(), document.getElementsByTagName("body")[0]
  else
    React.renderComponent LoginPage(), document.getElementsByTagName("body")[0]

window.init = init
