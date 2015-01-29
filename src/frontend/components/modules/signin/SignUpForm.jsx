/**
 * cardiomagnyl
 */

var React = require('react');
var moment = require('moment');
var BooleanRadioGroup = require('../../form/BooleanRadioGroup');
var DateInput = require('../../form/DateInput');
var Select = require('../../form/Select');
var apiRequest = require('../../../utilities/apiRequest');
var FormMixin = require('vstack-form').FormMixin;
var signUpForm = require('./signUpForm');

var SignUpForm = React.createClass({
  mixins: [FormMixin],

  getInitialState: function() {
    return {
      signUpProcess: false
    };
  },

  getInitialFormState: function() {
    var form = signUpForm({
      firstname: '',
      lastname: '',
      email: '',
      plainPassword: '',
      birthday: '',
      region: null,
      isDoctor: false,
      specializationName: '',
      specializationExperienceYears: '',
      specializationInstitutionAddress: '',
      specializationInstitutionPhone: '',
      specializationInstitutionName: '',
      specializationGraduationDate: '',
      isSubscribed: true,
      agreedPersonal: true,
      agreedRecommendation: true
    });

    return form.markBlured('birthday')
               .markBlured('region')
               .markBlured('specializationExperienceYears')
               .markBlured('specializationGraduationDate')
               .markBlured('agreedPersonal')
               .markBlured('isSubscribed')
               .markBlured('agreedRecommendation');
  },

  signUp: function(event) {
    event.preventDefault();

    if (this.state.signUpProcess) {
      return;
    }

    if (this.isFormInvalid()) {
      this.setState({
        formState: this.state.formState.markAll()
      });
      return;
    }

    this.setState({signUpProcess: true});

    var formData = this.state.formState.data;

    var data = {
      firstname: formData.firstname,
      lastname: formData.lastname,
      email: formData.email,
      plainPassword: formData.plainPassword,
      birthday: formData.birthday.format('YYYY-MM-DDThh:mm:ssZ'),
      isDoctor: formData.isDoctor,
      specializationName: null,
      specializationExperienceYears: null,
      specializationInstitutionAddress: null,
      specializationInstitutionPhone: null,
      specializationInstitutionName: null,
      specializationGraduationDate: null,
      isSubscribed: formData.isSubscribed,
      links: {
        region: String(formData.region)
      }
    };

    if (formData.isDoctor) {
      data.specializationName = formData.specializationName;
      data.specializationExperienceYears = formData.specializationExperienceYears;
      data.specializationInstitutionAddress = formData.specializationInstitutionAddress;
      data.specializationInstitutionPhone = formData.specializationInstitutionPhone;
      data.specializationInstitutionName = formData.specializationInstitutionName;
      data.specializationGraduationDate = formData.specializationGraduationDate.format('YYYY-MM-DDThh:mm:ssZ');
    }

    apiRequest('POST', '/api/v1/users', data)
                .then(function() {
                  window.location.href = window.location.href;
                }.bind(this))
                .then(null, function(res) {
                  this.setState({signUpProcess: false});
                  try {
                    alert(res.responseJSON.errors[0].title);
                  } catch(e) {
                    alert('Unknown error');
                  }
                }.bind(this));
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

  getRegionOptions: function() {
    var options =  this.props.regions
               .map(function(region) {
                 return {
                   value: region.id,
                   hash: String(region.id),
                   text: region.name
                 };
               });

    if (!this.state.formState.data.region) {
      options.unshift({value: null, hash: '', text: ''});
    }

    return options;
  },

  renderDoctorPart: function() {
    return (
      <div>
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
      </div>
    );
  },

  render: function() {
    return (
      <form className="fieldset fieldset_label-w30p" onSubmit={this.signUp}>
        <div className="fieldset__in">
          <div className={"field field_rows " + (this.isFieldErrorVisible('firstname') && 'is-error' || '')}>
            <div className="field__label">Ваши данные</div>
            <div className="field__in">
              <input className="input" type="text" placeholder="Имя"
                     valueLink={this.linkForm('firstname')}
                     onBlur={this.formBlurMaker('firstname')} />
              {this.isFieldErrorVisible('firstname') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field field_wide " + (this.isFieldErrorVisible('lastname') && 'is-error' || '')}>
            <div className="field__in">
              <input className="input" type="text" placeholder="Фамилия"
                     valueLink={this.linkForm('lastname')}
                     onBlur={this.formBlurMaker('lastname')} />
              {this.isFieldErrorVisible('lastname') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field field_wide " + (this.isFieldErrorVisible('email') && 'is-error' || '')}>
            <div className="field__in">
              <input className="input" type="text" placeholder="Электронная почта"
                     onBlur={this.formBlurMaker('email')}
                     valueLink={this.linkForm('email')} />
              {this.isFieldErrorVisible('email') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field field_wide " + (this.isFieldErrorVisible('plainPassword') && 'is-error' || '')}>
            <div className="field__in">
              <input className="input" type="password" placeholder="Пароль"
                     onBlur={this.formBlurMaker('plainPassword')}
                     valueLink={this.linkForm('plainPassword')} />
              {this.isFieldErrorVisible('plainPassword') && <i className="icon icon-attention-fill"></i>}
            </div>
          </div>
          <div className={"field " + (this.isFieldErrorVisible('birthday') && 'is-error' || '')}>
            <div className="field__label">Дата рождения</div>
            <div className="field__in">
              <DateInput valueLink={this.linkForm('birthday')} max={moment().subtract(18, 'years')} />
              {this.isFieldErrorVisible('birthday') && <i className="icon icon-attention-fill"></i>}
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
          <div className="field field_radio-row field_label-w60p">
            <div className="field__label">Вы являетесь врачом?</div>
            <div className="field__in">
              <BooleanRadioGroup valueLink={this.linkForm('isDoctor')} />
            </div>
          </div>
          {this.state.formState.data.isDoctor && this.renderDoctorPart()}
          <div className="field field_checkbox-list" style={{borderTop: '1px solid #d9d9d9', paddingTop: 20}}>
            <label className={"field__checkbox " + (this.isFieldErrorVisible('agreedPersonal') && 'is-error' || '')}>
              <input type="checkbox" checkedLink={this.linkForm('agreedPersonal')} />
              <span>
                Согласен на обработку персональных данных
                <i className="icon icon-ok"></i>
              </span>
            </label>
            <label className="field__checkbox">
              <input type="checkbox" checkedLink={this.linkForm('isSubscribed')} />
              <span>
                Согласен получать информацию по email
                <i className="icon icon-ok"></i>
              </span>
            </label>
            <label className={"field__checkbox " + (this.isFieldErrorVisible('agreedRecommendation') && 'is-error' || '')}>
              <input type="checkbox" checkedLink={this.linkForm('agreedRecommendation')} />
              <span>
                Согласен с тем, что вся информация носит рекомендательный характер
                <i className="icon icon-ok"></i>
              </span>
            </label>
          </div>
          <div className="btn-center">
            <button className="button">Зарегистрироваться</button>
          </div>
        </div>
      </form>
    );
  }
});

module.exports = SignUpForm;
