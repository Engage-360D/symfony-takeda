/**
 * cardiomagnyl
 */

var React = require('react');
var SignInForm = require('./SignInForm');
var SignUpForm = require('./SignUpForm');

var SignIn = React.createClass({
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
            <SignInForm />
          </div>
          <div className="l__column">
            <div className="h h_2">Регистрация</div>
            <SignUpForm regions={this.props.regions} />
          </div>
        </div>
      </div>
    );
  }
});

module.exports = SignIn;
