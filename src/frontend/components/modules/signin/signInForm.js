/**
 * cardiomagnyl
 */

var createForm = require('vstack-form').createForm;
var signInValidator = require('./signInValidator');

var signInForm = createForm(signInValidator);

module.exports = signInForm;
