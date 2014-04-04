moment = require "moment"

validationConstraints =
  notBlank: ->
    (value) -> value?.length > 0

  isTrue: ->
    (value) -> value is true

  email: ->
    (value) -> /^.+@.+\..+$/.test value

  date: (format) ->
    (value) -> moment(value, format or "DD.MM.YYYY").isValid()

module.exports = validationConstraints
