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
      <div>
        {this.props.testResults.map(this.renderTestResult)}
      </div>
    );
  }
});

module.exports = TestResultList;
