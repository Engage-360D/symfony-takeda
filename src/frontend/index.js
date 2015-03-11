/**
 * cardiomagnyl
 */

module.exports = {
  $: require('jquery'),
  render: require('./render'),
  SignIn: require('./components/modules/signin/SignIn'),
  SignInAfterSocial: require('./components/modules/signin/SignInAfterSocial'),
  RiskAnalysis: require('./components/modules/riskAnalysis/RiskAnalysis'),
  OpinionList: require('./components/modules/pressCenter/OpinionList'),
  NewsList: require('./components/modules/pressCenter/NewsList'),
  TestResultList: require('./components/modules/riskAnalysis/TestResultList'),
  Settings: require('./components/modules/account/Settings'),
  Timeline: require('./components/modules/account/Timeline'),
  Diet: require('./components/modules/account/Diet'),
  Institutions: require('./components/modules/intitutions/Institutions'),
  InstitutionsMap: require('./components/modules/intitutions/InstitutionsMap'),
  Report: require('./components/modules/account/Report')
};
