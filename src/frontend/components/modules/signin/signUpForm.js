/**
 * cardiomagnyl
 */

var createForm = require('vstack-form').createForm;
var signUpValidator = require('./signUpValidator');

var signUpForm = createForm(signUpValidator);

module.exports = signUpForm;
