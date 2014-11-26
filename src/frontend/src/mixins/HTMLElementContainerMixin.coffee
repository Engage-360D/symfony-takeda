HTMLElementContainerMixin =
  createContainer: ->
    body = @getBody()
    container = document.createElement "div"
    body.appendChild container
    container

  getBody: ->
    if document.querySelectorAll
      body = document.querySelectorAll "body"
    else
      body = document.getElementsByTagName "body"
    if body[0] then body[0] else body

module.exports = HTMLElementContainerMixin
