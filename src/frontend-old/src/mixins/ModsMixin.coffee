ModsMixin =
  getClassName: (baseClassName, defaultMods) ->
    classNames = [baseClassName]

    mods = if typeof @props.mods is "string"
      @props.mods.split " "
    else if Array.isArray @props.mods
      @props.mods
    else if defaultMods
      defaultMods
    else
      []

    for mod in mods
      className = [baseClassName, mod].join "-"
      classNames.push className unless className in classNames

    classNames.join " "


module.exports = ModsMixin
