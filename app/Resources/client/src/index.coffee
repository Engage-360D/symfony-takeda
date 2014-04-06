React = require "react"


sharedComponents =
  Test: require "./components/modules/Test"
  Registration: require "./components/modules/Registration"
  ResetPassword: require "./components/modules/ResetPassword"


getNodes = ->
  if document.querySelectorAll
    document.querySelectorAll "[data-react-component]"
  else
    document.getElementsByTagName "*"


document.addEventListener "DOMContentLoaded", ->
  for node in getNodes()
    componentName = node.getAttribute "data-react-component"

    if componentName and sharedComponents[componentName]
      Component = sharedComponents[componentName]

      props = {}
      for attribute in node.attributes
        if attribute.nodeName isnt "data-react-component"
          propKey = attribute.nodeName.replace(/^data-/, "").replace(/-(.)/g, (g) -> g[1].toUpperCase())
          props[propKey] = attribute.nodeValue

      React.renderComponent Component(props), node
