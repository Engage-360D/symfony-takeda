/**
 * cardiomagnyl
 */

var React = require('react');
var apiRequest = require('../../../utilities/apiRequest');
var signInForm = require('./signInForm.js');
var FormMixin = require('vstack-form').FormMixin;

var SignInForm = React.createClass({
  mixins: [FormMixin],

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

    apiRequest('POST', '/api/v1/tokens', {
      email: this.state.formState.data.email,
      plainPassword: this.state.formState.data.plainPassword
    })
                 .then(function() {
                   window.location.href = window.location.href;
                 }.bind(this))
                 .then(null, function(res) {
                   this.setState({signInProcess: false});
                   try {
                     alert(res.responseJSON.errors[0].title);
                   } catch(e) {
                     alert('Unknown error');
                   }
                 }.bind(this));
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
        <div className="field field_rows" style={{position: 'absolute', marginTop: -10}}>
          <div className="field__in">
            <ul className="social social_dark">
              <li><a className="social__vk" href="#"><i className="icon icon-soc-vk"></i></a></li>
              <li><a className="social__fb" href="#"><i className="icon icon-soc-fb"></i></a></li>
              <li><a className="social__ok" href="#"><i className="icon icon-soc-ok"></i></a></li>
            </ul>
          </div>
        </div>
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
