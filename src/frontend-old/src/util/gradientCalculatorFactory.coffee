module.exports = gradientCalculatorFactory = (start, end) ->
  startRed = start[0]
  startGreen = start[1]
  startBlue = start[2]
  
  endRed = end[0]
  endGreen = end[1]
  endBlue = end[2]

  (time) ->
    red = Math.round time * startRed + (1-time) * endRed
    green = Math.round time * startGreen + (1-time) * endGreen
    blue = Math.round time * startBlue + (1-time) * endBlue
    [red, green, blue]
