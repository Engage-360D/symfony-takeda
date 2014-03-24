validationConstraints =
  notBlank: ->
    (value) -> value?.length > 0

  isTrue: ->
    (value) -> value is true


module.exports = validationConstraints
