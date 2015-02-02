/**
 * cardiomagnyl
 */

var React = require('react');
var SignInForm = require('./SignInForm');
var SignUpForm = require('./SignUpForm');

var SignInAfterSocial = React.createClass({
  render: function() {
    return (
      <div className="container">
        <div className="h h_2">Регистрация</div>
        <SignUpForm regions={this.props.regions} auth={this.props.auth} />
        <div className="h h_2">Вход</div>
        <SignInForm hideSocial auth={this.props.auth} />
      </div>
    );
  }
});

module.exports = SignInAfterSocial;
