/**
 * cardiomagnyl
 */

var validator = require('vstack-validator');

var constraints = validator.constraints;

var signInValidator = constraints.object({
  mapping: {
    email: constraints.all({
      validators: {
        notEmpty: constraints.notEmpty(),
        email: constraints.email()
      }
    }),
    plainPassword: constraints.notEmpty()
  }
});

module.exports = signInValidator;
