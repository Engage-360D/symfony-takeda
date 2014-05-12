ErrorMessageMixin =
  getErrorMessage: (type, config) ->
    minLength = if config and config.minLength then config.minLength else 3
    minGrowth = if config and config.minGrowth then config.minGrowth else 30
    maxGrowth = if config and config.maxGrowth then config.maxGrowth else 700
    minWeight = if config and config.minWeight then config.minWeight else 30
    maxWeight = if config and config.maxWeight then config.maxWeight else 300

    errors =
      blank: "Заполните поле"
      minLength: "Минимальная длина #{minLength} символа"
      minGrowth: "Минимальный рост - #{minGrowth} см"
      maxGrowth: "Максимальный рост - #{maxGrowth} см"
      minWeight: "Минимальный вес - #{minWeight} кг"
      maxWeight: "Максимальный вес - #{maxWeight} кг"
      email: "Некорректный email адресс"
      invalidFormat: "Неверный формат"
      default: "Ошибка заполнения формы"
      unknown: "При попытке авторизации произошла ошибка. Попробуйте позже."

    return if errors[type] then errors[type] else errors.default

module.exports = ErrorMessageMixin