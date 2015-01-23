compareRegions = (a, b) ->
  if a and b and a.text and b.text
    if a.text is 'Москва' or b.text is 'Москва'
      return if a.text is 'Москва' then -1 else 1
    else if a.text is 'Санкт-Петербург' or b.text is 'Санкт-Петербург'
      return if a.text is 'Санкт-Петербург' then -1 else 1
    else
      if a.text < b.text
        return -1
      else if a.text > b.text
        return 1
      else
        return 0

module.exports = compareRegions