moment = require "moment"


isNullOrUndefined = (value) ->
  value is null or value is undefined


validationConstraints =
  notNull: ->
    (value) ->
      not isNullOrUndefined value

  notEmpty: ->
    (value) ->
      return true if isNullOrUndefined value
      value.length > 0

  isTrue: ->
    (value) ->
      return true if isNullOrUndefined value
      value is true

  email: ->
    (value) ->
      return true if isNullOrUndefined value
      /^.+@.+\..+$/.test value

  date: (format) ->
    (value) ->
      return true if isNullOrUndefined value
      moment(value, format or "DD.MM.YYYY").isValid()

  min: (min) ->
    (value) ->
      return true if isNullOrUndefined value
      value >= min

  max: (max) ->
    (value) ->
      return true if isNullOrUndefined value
      value <= max

  minLength: (length) ->
    (value) ->
      return false unless value
      value.length >= length

module.exports = validationConstraints
