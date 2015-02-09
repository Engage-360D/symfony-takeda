/**
 * cardiomagnyl
 */

module.exports = {
  render: require('./render'),
  SignIn: require('./components/modules/signin/SignIn'),
  SignInAfterSocial: require('./components/modules/signin/SignInAfterSocial'),
  RiskAnalysis: require('./components/modules/riskAnalysis/RiskAnalysis'),
  OpinionList: require('./components/modules/pressCenter/OpinionList'),
  NewsList: require('./components/modules/pressCenter/NewsList'),
  TestResultList: require('./components/modules/riskAnalysis/TestResultList'),
  Settings: require('./components/modules/settings/Settings')
};
