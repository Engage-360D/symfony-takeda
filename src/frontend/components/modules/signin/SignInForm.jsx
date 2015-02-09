/**
 * cardiomagnyl
 */

var React = require('react');
var apiRequest = require('../../../utilities/apiRequest');
var signInForm = require('./signInForm.js');
var FormMixin = require('vstack-form').FormMixin;
var social = require('../../../utilities/social');

var SignInForm = React.createClass({
  mixins: [FormMixin],

  getDefaultProps: function() {
    return {
      hideSocial: false
    };
  },

  getInitialState: function() {
    return {
      signInProcess: false
    };
  },

  getInitialFormState: function() {
    return signInForm({
      email: '',
      plainPassword: ''
    });
  },

  signIn: function(event) {
    event.preventDefault();

    if (this.state.signInProcess) {
      return;
    }

    if (this.isFormInvalid()) {
      this.setState({
        formState: this.state.formState.markAll()
      });
      return;
    }

    this.setState({signInProcess: true});

    var error = function(res) {
      this.setState({signInProcess: false});
      try {
        alert(res.responseJSON.errors[0].title);
      } catch(e) {
        alert('Unknown error');
      }
    }.bind(this);
    var done = function() {
      if (window.opener) {
        window.opener.authDone();
        window.close();
      } else if (this.props.onAuthDone) {
        this.props.onAuthDone();
      }
    }.bind(this);

    apiRequest('POST', '/api/v1/tokens', {
      email: this.state.formState.data.email,
      plainPassword: this.state.formState.data.plainPassword
    }, function(err, data) {
      if (err) {
        return error(err);
      }

      if (!this.props.auth) {
        return done();
      }

      apiRequest('PUT', '/api/v1/account', this.props.auth, function(err, date) {
        if (err) {
          return error(err);
        }

        done();
      });

    }.bind(this))
  },

  openVk: function(event) {
    event.preventDefault();
    social.openVk(function() {
      this.props.onAuthDone();
    }.bind(this));
  },

  openFb: function(event) {
    event.preventDefault();
    social.openFb(function() {
      this.props.onAuthDone();
    }.bind(this));
  },

  renderSocial: function() {
    return (
      <div className="field field_rows" style={{position: 'absolute', marginTop: -10}}>
        <div className="field__in">
          <ul className="social social_dark">
            <li><a className="social__vk" href="#" onClick={this.openVk}><i className="icon icon-soc-vk"></i></a></li>
            <li><a className="social__fb" href="#" onClick={this.openFb}><i className="icon icon-soc-fb"></i></a></li>
            <li><a className="social__ok" href="#"><i className="icon icon-soc-ok"></i></a></li>
          </ul>
        </div>
      </div>
    );
  },

  render: function() {
    return (
      <form className="fieldset" onSubmit={this.signIn}>
        <div className={"field field_rows " + (this.isFieldErrorVisible('email') && 'is-error' || '')}>
          <div className="field__in">
            <input className="input" type="text" placeholder="Имя"
                   valueLink={this.linkForm('email')}
                   onBlur={this.formBlurMaker('email')} />
            {this.isFieldErrorVisible('email') && <i className="icon icon-attention-fill"></i>}
          </div>
        </div>
        <div className={"field field_rows " + (this.isFieldErrorVisible('plainPassword') && 'is-error' || '')}>
          <div className="field__in">
            <input className="input" type="password" placeholder="******"
                   valueLink={this.linkForm('plainPassword')}
                   onBlur={this.formBlurMaker('plainPassword')} />
            {this.isFieldErrorVisible('plainPassword') && <i className="icon icon-attention-fill"></i>}
          </div>
        </div>
        {!this.props.hideSocial && this.renderSocial()}
        <div className="field field_right">
          <div className="field__in">
            <a className="link link_black" href="#"><span>Забыли пароль</span><i className="icon icon-arr-circle-right"></i><i className="icon icon-arr-circle-right-fill"></i></a>
          </div>
        </div>
        <div className="btn-center">
          <button className="button" disabled={this.state.signInProcess}>Войти</button>
        </div>
      </form>
    );
  }
});

module.exports = SignInForm;
