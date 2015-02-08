var React = require('react');
var TestResult = require('./TestResult');

var TestResultList = React.createClass({
  renderTestResult: function(testResult) {
    return (
      <div style={{marginTop: 50}}>
        <TestResult testResult={testResult} />
      </div>
    );
  },

  render: function() {
    return (
      <div className="container">
        <div className="head">
          <div className="head__right">
            <div className="head__city">
              <a href="#"><i className="icon icon-marker"></i><span>Москва</span></a>
            </div>
          </div>
          <div className="head__left">
            <div className="h">Мои рекомендации</div>
          </div>
        </div>
        {this.props.testResults.map(this.renderTestResult)}
      </div>
    );
  }
});

module.exports = TestResultList;
