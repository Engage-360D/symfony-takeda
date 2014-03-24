validateValue = (config, args...) ->
  fieldValidity =
    valid: true
    invalid: false

  for validatorName in Object.keys(config)
    valid = config[validatorName].apply null, args

    fieldValidity[validatorName] =
      valid: valid
      invalid: not valid

    unless valid
      fieldValidity.valid = false
      fieldValidity.invalid = true

  fieldValidity


validateState = (config, state) ->
  validity =
    valid: true
    invalid: false
    children: {}

  if config.children
    for key in Object.keys(config.children)
      childrenValidity = validateValue config.children[key], state[key]

      validity.children[key] = childrenValidity

      if childrenValidity.invalid
        validity.valid = false
        validity.invalid = true

  if config.component
    componentValidity = validateValue config.component, state, validity.children

    validity.component = componentValidity

    if componentValidity.invalid
      validity.valid = false
      validity.invalid = true

  validity


ValidationMixin =
  componentWillMount: ->
    @validity = validateState @getValidationConfig(), @state

  componentWillUpdate: (nextProps, nextState) ->
    @_prevValidity = @validity
    @validity = validateState @getValidationConfig(), nextState

  componentDidUpdate: ->
    @componentDidValidated @_prevValidity if @componentDidValidated


module.exports = ValidationMixin
