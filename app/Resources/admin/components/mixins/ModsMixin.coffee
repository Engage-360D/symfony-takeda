
ModsMixin =
  getClassName: (baseClassName, defaultMods) ->
    classNames = [baseClassName]

    if typeof @props.mods is "string"
      mods = @props.mods.split " "
    else if Array.isArray @props.mods
      mods = @props.mods
    else
      mods = defaultMods or []

    mods.forEach (mod) ->
      className = [baseClassName, mod].join "-"

      if classNames.indexOf(className) is -1
        classNames.push className

    classNames.join " "


module.exports = ModsMixin
