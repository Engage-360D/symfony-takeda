/**
 * cardiomagnyl
 */

var React = require('react');
var SignInForm = require('./SignInForm');
var SignUpForm = require('./SignUpForm');

var SignIn = React.createClass({
  onAuthDone: function() {
    window.location.href = window.location.href;
    window.location.reload();
  },

  render: function() {
    return (
      <div className="container">
        <div className="head">
          <div className="head__right">
            <div className="head__city">
              <a href="#">
                <i className="icon icon-marker"></i>
                <span>Москва</span>
              </a>
            </div>
          </div>
          <div className="head__left">
            <div className="h">Личный кабинет</div>
          </div>
        </div>
        <div className="l">
          <div className="l__column">
            <div className="h h_2">Вход</div>
            <SignInForm onAuthDone={this.onAuthDone} />
          </div>
          <div className="l__column">
            <div className="h h_2">Регистрация</div>
            <SignUpForm onAuthDone={this.onAuthDone} regions={this.props.regions} />
          </div>
        </div>
      </div>
    );
  }
});

module.exports = SignIn;
