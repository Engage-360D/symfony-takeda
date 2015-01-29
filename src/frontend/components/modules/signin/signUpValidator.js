/**
 * cardiomagnyl
 */

var validator = require('vstack-validator');
var moment = require('moment');

var createConstraint = validator.createConstraint;
var constraints = validator.constraints;

function createValidator(name, validator, message) {
  return createConstraint({
    name: name,
    validator: function(value, spec, root) {
      var validity = validator(value, spec, root);
      if (typeof validity === 'boolean') {
        validity = {valid: validity};
      }
      validity.message = message || 'Error';
      return validity;
    }
  })();
}

var isMoment = createConstraint({
  name: 'isMoment',
  validator: function(value, spec, root) {
    return {
      valid: moment.isMoment(value),
      message: 'Invalid moment'
    };
  }
});

var signUpValidator = constraints.object({
  mapping: {
    firstname: constraints.notEmpty(),
    lastname: constraints.notEmpty(),
    email: constraints.all({
      validators: {
        notEmpty: constraints.notEmpty(),
        email: constraints.email()
      }
    }),
    plainPassword: constraints.notEmpty(),
    birthday: isMoment(),
    region: constraints.all({
      validators: {
        notNull: constraints.notNull(),
        isNumber: constraints.isNumber()
      }
    }),
    specializationName: createValidator('specializationName', function(value, spec, root) {
      if (!root.isDoctor) return true;
      return constraints.notEmpty()(value, spec, root);
    }),
    specializationExperienceYears: createValidator('specializationExperienceYears', function(value, spec, root) {
      if (!root.isDoctor) return true;
      return constraints.all({
        validators: {
          notNull: constraints.notNull(),
          isNumber: constraints.isNumber()
        }
      })(value, spec, root);
    }),
    specializationInstitutionAddress: createValidator('specializationInstitutionAddress', function(value, spec, root) {
      if (!root.isDoctor) return true;
      return constraints.notEmpty()(value, spec, root);
    }),
    specializationInstitutionPhone: createValidator('specializationInstitutionPhone', function(value, spec, root) {
      if (!root.isDoctor) return true;
      return constraints.notEmpty()(value, spec, root);
    }),
    specializationInstitutionName: createValidator('specializationInstitutionName', function(value, spec, root) {
      if (!root.isDoctor) return true;
      return constraints.notEmpty()(value, spec, root);
    }),
    specializationGraduationDate: createValidator('specializationGraduationDate', function(value, spec, root) {
      if (!root.isDoctor) return true;
      return isMoment()(value, spec, root);
    }),
    agreedPersonal: constraints.isTrue(),
    agreedRecommendation: constraints.isTrue()
  }
});

module.exports = signUpValidator;
