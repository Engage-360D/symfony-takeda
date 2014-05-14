ErrorMessageMixin =
  getErrorMessage: (type, config) ->
    minLength = if config and config.minLength then config.minLength else 3
    errors =
      blank: "Заполните поле"
      minLength: "Минимальная длина #{minLength} символа"
      email: "Некорректный email адресс"
      default: "Ошибка заполнения формы"
      unknown: "При попытке авторизации произошла ошибка. Попробуйте позже."

    return if errors[type] then errors[type] else errors.default

module.exports = ErrorMessageMixin