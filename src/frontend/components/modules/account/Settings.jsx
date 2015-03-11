var React = require('react');
var LinkedStateMixin = require('react/lib/LinkedStateMixin');
var social = require('../../../utilities/social');
var apiRequest = require('../../../utilities/apiRequest');
var Select = require('../../form/Select');
var DateInput = require('../../form/DateInput');
var validator = require('vstack-validator');
var createForm = require('vstack-form').createForm;
var FormMixin = require('vstack-form').FormMixin;
var moment = require('moment');

var createConstraint = validator.createConstraint;
var constraints = validator.constraints;

var isMoment = createConstraint({
  name: 'isMoment',
  validator: function(value, spec, root) {
    return {
      valid: moment.isMoment(value),
      message: 'Invalid moment'
    };
  }
});

var generalValidator = constraints.object({
  mapping: {
    firstname: constraints.notEmpty(),
    lastname: constraints.notEmpty(),
    email: constraints.all({
      validators: {
        notEmpty: constraints.notEmpty(),
        email: constraints.email()
      }
    }),
    region: constraints.all({
      validators: {
        notNull: constraints.notNull(),
        isString: constraints.isString()
      }
    })
  }
});

var specializationValidator = constraints.object({
  mapping: {
    specializationName: constraints.notEmpty(),
    specializationExperienceYears: constraints.all({
      validators: {
        notNull: constraints.notNull(),
        isNumber: constraints.isNumber()
      }
    }),
    specializationInstitutionAddress: constraints.notEmpty(),
    specializationInstitutionPhone: constraints.notEmpty(),
    specializationInstitutionName: constraints.notEmpty(),
    specializationGraduationDate: isMoment()
  }
});

var generalForm = createForm(generalValidator);
var specializationForm = createForm(specializationValidator);

var GeneralForm = React.createClass({
  mixins: [FormMixin],

  getInitialState: function() {
    return {
      disabled: false
    };
  },

  getInitialFormState: function() {
    return generalForm({
      firstname: this.props.user.firstname,
      lastname: this.props.user.lastname,
      email: this.props.user.email,
      region: this.props.user.links.region
    }).markBlured('region');
  },

  getRegionOptions: function() {
    var options =  this.props.regions
               .map(function(region) {
                 return {
                   value: String(region.id),
                   hash: String(region.id),
                   text: region.name
                 };
               });

    if (!this.state.formState.data.region) {
      options.unshift({value: null, hash: '', text: ''});
    }

    return options;
  },

  save: function(event) {
    event.preventDefault();

    if (this.state.disabled) {
      return;
    }

    if (this.isFormInvalid()) {
      this.setState({
        formState: this.state.formState.markAll()
      });
      return;
    }

    this.setState({disabled: true});

    var error = function(res) {
      this.setState({disabled: false});
      try {
        alert(res.responseJSON.errors[0].title);
      } catch(e) {
        alert('Unknown error');
      }
    }.bind(this);
    var done = function() {
      this.setState({disabled: false});
      alert('Ваши данные успешно сохранены');
    }.bind(this);

    apiRequest('PUT', '/api/v1/account', {
      firstname: this.state.formState.data.firstname,
      lastname: this.state.formState.data.lastname,
      email: this.state.formState.data.email,
      links: {
        region: this.state.formState.data.region
      }
    }, function(err, data) {
      if (err) {
        return error(err);
      }

      done();
    });
  },

  render: function() {
    return (
      <form className="fieldset fieldset_label-w105" onSubmit={this.save}>
        <div className="fieldset__title">Общая информация</div>
        <div className="fieldset__in">
          <div className={"field " + (this.isFieldErrorVisible('firstname') && 'is-error' || '')}>
            <div className="field__label">Имя</div>
            <div className="field__in">
              <input className="input" type="text" placeholder="Имя"
                     valueLink={this.linkForm('firstname')}
                     onBlur={this.formBlurMaker('firstname')} />
              {this.isFieldErrorVisible('firstname') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field " + (this.isFieldErrorVisible('lastname') && 'is-error' || '')}>
            <div className="field__label">Фамилия</div>
            <div className="field__in">
              <input className="input" type="text" placeholder="Фамилия"
                     valueLink={this.linkForm('lastname')}
                     onBlur={this.formBlurMaker('lastname')} />
              {this.isFieldErrorVisible('lastname') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field " + (this.isFieldErrorVisible('email') && 'is-error' || '')}>
            <div className="field__label">Email</div>
            <div className="field__in">
              <input className="input" type="text" placeholder="Email"
                     valueLink={this.linkForm('email')}
                     onBlur={this.formBlurMaker('email')} />
              {this.isFieldErrorVisible('email') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field " + (this.isFieldErrorVisible('region') && 'is-error' || '')}>
            <div className="field__label">Регион</div>
            <div className="field__in">
              <Select options={this.getRegionOptions()}
                      valueLink={this.linkForm('region')} />
              {this.isFieldErrorVisible('region') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className="field">
            <div className="field__label"></div>
            <div className="field__in">
              <button className="button">Сохранить</button>
            </div>
          </div>
        </div>
      </form>
    );
  }
});

var SpecializationForm = React.createClass({
  mixins: [FormMixin],

  getInitialState: function() {
    return {
      disabled: false
    };
  },

  getInitialFormState: function() {
    return specializationForm({
      specializationName: this.props.user.specializationName,
      specializationExperienceYears: this.props.user.specializationExperienceYears,
      specializationInstitutionAddress: this.props.user.specializationInstitutionAddress,
      specializationInstitutionPhone: this.props.user.specializationInstitutionPhone,
      specializationInstitutionName: this.props.user.specializationInstitutionName,
      specializationGraduationDate: moment(this.props.user.specializationGraduationDate)
    }).markBlured('specializationExperienceYears').markBlured('specializationGraduationDate');
  },

  getSpecializationExperienceYearsOptions: function() {
    var options = [];

    for (var i = 60; i >= 1; i--) {
      options.push({value: i, hash: String(i), text: String(i)});
    }

    if (!this.state.formState.data.specializationExperienceYears) {
      options.unshift({value: null, hash: '', text: ''});
    }

    return options;
  },

  save: function(event) {
    event.preventDefault();

    if (this.state.disabled) {
      return;
    }

    if (this.isFormInvalid()) {
      this.setState({
        formState: this.state.formState.markAll()
      });
      return;
    }

    this.setState({disabled: true});

    var error = function(res) {
      this.setState({disabled: false});
      try {
        alert(res.responseJSON.errors[0].title);
      } catch(e) {
        alert('Unknown error');
      }
    }.bind(this);
    var done = function() {
      this.setState({disabled: false});
      alert('Ваши данные успешно сохранены');
    }.bind(this);

    apiRequest('PUT', '/api/v1/account', {
      specializationName: this.state.formState.data.specializationName,
      specializationExperienceYears: this.state.formState.data.specializationExperienceYears,
      specializationInstitutionAddress: this.state.formState.data.specializationInstitutionAddress,
      specializationInstitutionPhone: this.state.formState.data.specializationInstitutionPhone,
      specializationInstitutionName: this.state.formState.data.specializationInstitutionName,
      specializationGraduationDate: this.state.formState.data.specializationGraduationDate.format()
    }, function(err, data) {
      if (err) {
        return error(err);
      }

      done();
    });
  },

  render: function() {
    return (
      <form className="fieldset fieldset_label-w105" onSubmit={this.save}>
        <div className="fieldset__title">Специализация</div>
        <div className="fieldset__in">
          <div className={"field field_rows " + (this.isFieldErrorVisible('specializationName') && 'is-error' || '')}>
            <div className="field__label" style={{width: '100%'}}>Основная специализация</div>
            <div className="field__in">
              <input className="input" type="text" placeholder="Введите название"
                     onBlur={this.formBlurMaker('specializationName')}
                     valueLink={this.linkForm('specializationName')} />
              {this.isFieldErrorVisible('specializationName') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field " + (this.isFieldErrorVisible('specializationExperienceYears') && 'is-error' || '')}>
            <div className="field__label">Стаж</div>
            <div className="field__in">
              <Select options={this.getSpecializationExperienceYearsOptions()}
                      valueLink={this.linkForm('specializationExperienceYears')} />
              {this.isFieldErrorVisible('specializationExperienceYears') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field " + (this.isFieldErrorVisible('specializationInstitutionAddress') && 'is-error' || '')}>
            <div className="field__label">Адрес</div>
            <div className="field__in">
              <input className="input" type="text"
                     onBlur={this.formBlurMaker('specializationInstitutionAddress')}
                     valueLink={this.linkForm('specializationInstitutionAddress')} />
              {this.isFieldErrorVisible('specializationInstitutionAddress') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field " + (this.isFieldErrorVisible('specializationInstitutionPhone') && 'is-error' || '')}>
            <div className="field__label">Телефон</div>
            <div className="field__in">
              <input className="input" type="text"
                     onBlur={this.formBlurMaker('specializationInstitutionPhone')}
                     valueLink={this.linkForm('specializationInstitutionPhone')} />
              {this.isFieldErrorVisible('specializationInstitutionPhone') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field " + (this.isFieldErrorVisible('specializationInstitutionName') && 'is-error' || '')}>
            <div className="field__label">Учебное заведение</div>
            <div className="field__in">
              <input className="input" type="text"
                     onBlur={this.formBlurMaker('specializationInstitutionName')}
                     valueLink={this.linkForm('specializationInstitutionName')} />
              {this.isFieldErrorVisible('specializationInstitutionName') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field " + (this.isFieldErrorVisible('specializationGraduationDate') && 'is-error' || '')}>
            <div className="field__label">Год окончания</div>
            <div className="field__in">
              <DateInput valueLink={this.linkForm('specializationGraduationDate')} max={moment().subtract(1, 'day')} />
              {this.isFieldErrorVisible('specializationGraduationDate') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className="field">
            <div className="field__label"></div>
            <div className="field__in">
              <button className="button">Сохранить</button>
            </div>
          </div>
        </div>
      </form>
    );
  }
});

var Settings = React.createClass({
  mixins: [LinkedStateMixin],

  getInitialState: function() {
    console.dir(this.props.user);
    return {
      firstname: this.props.user.firstname,
      lastname: this.props.user.lastname,
      email: this.props.user.email
    };
  },

  onAuthDone: function() {
    window.location.href = window.location.href;
    window.location.reload();
  },

  openVk: function(event) {
    event.preventDefault();
    social.openVk(function() {
      this.onAuthDone();
    }.bind(this));
  },

  openFb: function(event) {
    event.preventDefault();
    social.openFb(function() {
      this.onAuthDone();
    }.bind(this));
  },

  openOk: function(event) {
    event.preventDefault();
    social.openOk(function() {
      this.onAuthDone();
    }.bind(this));
  },

  openGoogle: function(event) {
    event.preventDefault();
    social.openGoogle(function() {
      this.onAuthDone();
    }.bind(this));
  },

  resetPassword: function() {
    var error = function(res) {
      try {
        alert(res.responseJSON.errors[0].title);
      } catch(e) {
        alert('Unknown error');
      }
    }.bind(this);

    this.setState({resetDisabled: true});
    apiRequest('POST', '/api/v1/account/reset-password', {
      email: this.props.user.email
    }, function(err, data) {
      this.setState({resetDisabled: false});
      if (err) {
        return error(err);
      }

      alert('На указанный email адрес выслан новый пароль');
    }.bind(this));
  },

  render: function() {
    return (
      <div className="settings">
        <GeneralForm {...this.props} />

        <div className="fieldset fieldset_label-w105">
          <div className="fieldset__title">Изменение пароля</div>
          <div className="fieldset__in">
            <div className="field">
              <div className="field__label">Пароль</div>
              <div className="field__in">
                <button className="button button_wide"
                        disabled={this.state.resetDisabled}
                        onClick={this.resetPassword}>Отправить на почту новый пароль</button>
              </div>
            </div>
          </div>
        </div>

        <div className="fieldset fieldset_label-w160">
          <div className="fieldset__title">Социальные сети</div>
          <div className="fieldset__in">
            <div className="field">
              <div className="field__label">Заходить через социальные сети</div>
              <div className="field__in">
                <ul className="social social_dark">
                  {!this.props.user.vkontakteId &&
                    <li><a className="social__vk" href="#" onClick={this.openVk}><i className="icon icon-soc-vk"></i></a></li>
                  }
                  {!this.props.user.facebookId &&
                    <li><a className="social__fb" href="#" onClick={this.openFb}><i className="icon icon-soc-fb"></i></a></li>
                  }
                  {!this.props.user.odnoklassnikiId &&
                    <li><a className="social__ok" href="#" onClick={this.openOk}><i className="icon icon-soc-ok"></i></a></li>
                  }
                  {!this.props.user.googleId &&
                    <li><a className="social__gplus" href="#" onClick={this.openGoogle}><i className="icon icon-gplus"></i></a></li>
                  }
                </ul>
              </div>
            </div>
            <div className="field">
              <div className="field__label">Уже привязанные сети</div>
              <div className="field__in">
                <ul className="social social_dark">
                  {this.props.user.vkontakteId &&
                    <li><a className="social__vk" href={"https://vk.com/id" + this.props.user.vkontakteId} target="blank"><i className="icon icon-soc-vk"></i></a></li>
                  }
                  {this.props.user.facebookId &&
                    <li><a className="social__fb" href={"https://facebook.com/" + this.props.user.facebookId} target="blank"><i className="icon icon-soc-fb"></i></a></li>
                  }
                  {this.props.user.odnoklassnikiId &&
                    <li><a className="social__ok" href={"http://ok.ru/profile/" + this.props.user.odnoklassnikiId} target="blank"><i className="icon icon-soc-ok"></i></a></li>
                  }
                  {this.props.user.googleId &&
                  <li><a className="social__gplus" href={"https://plus.google.com/" + this.props.user.googleId + "/posts"} target="blank"><i className="icon icon-gplus"></i></a></li>
                  }
                </ul>
              </div>
            </div>
          </div>
        </div>

        {this.props.user.roles.indexOf('ROLE_DOCTOR') >= 0 && <SpecializationForm {...this.props} />}
      </div>
    );
  }
});

module.exports = Settings;
