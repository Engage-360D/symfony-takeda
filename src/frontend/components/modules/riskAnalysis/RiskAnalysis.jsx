/**
 * cardiomagnyl
 */

var React = require('react');
var SignInForm = require('../signin/SignInForm');
var SignUpForm = require('../signin/SignUpForm');
var apiRequest = require('../../../utilities/apiRequest');
var BooleanRadioGroup = require('../../form/BooleanRadioGroup');
var RadioGroup = require('../../form/RadioGroup');
var DateInput = require('../../form/DateInput');
var Range = require('../../form/Range');
var NumberInput = require('../../form/NumberInput');
var TestResult = require('./TestResult');
var LargeSpin = require('engage-360d-spin/components/LargeSpin');
var validator = require('vstack-validator');
var createForm = require('vstack-form').createForm;
var FormMixin = require('vstack-form').FormMixin;
var moment = require('moment');

var constraints = validator.constraints;

var riskAnalysisValidator = constraints.object({
  mapping: {
    birthday: validator.createConstraint({
      name: 'isMoment',
      validator: function (value) {
        return {valid: moment.isMoment(value), message: 'Invalid moment'};
      }
    })(),
    growth: constraints.all({
      validators: [
        constraints.notEmpty(),
        constraints.isNumber(),
        validator.createConstraint({
          name: 'limits',
          validator: function (value) {
            return {valid: value >= 30 && value <= 300, message: 'Invalid moment'};
          }
        })()
      ]
    }),
    weight: constraints.all({
      validators: [
        constraints.notEmpty(),
        constraints.isNumber(),
        validator.createConstraint({
          name: 'limits',
          validator: function (value) {
            return {valid: value >= 30 && value <= 700, message: 'Invalid moment'};
          }
        })()
      ]
    })
  }
});

var riskAnalysisForm = createForm(riskAnalysisValidator);

var RiskAnalysis = React.createClass({
  mixins: [require('linked-state-mixin'), FormMixin],

  getInitialFormState: function() {
    return riskAnalysisForm({
      birthday: null,
      growth: null,
      weight: null
    }).markBlured('birthday');
  },

  getInitialState: function() {
    return {
      step: 'data',
      fetching: false,
      results: null,
      authError: false,
      error: false,
      user: null,
      sex: 'male',
      isSmoker: false,
      cholesterolLevel: 4,
      isCholesterolDrugsConsumer: false,
      isAcetylsalicylicDrugsConsumer: false,
      hasDiabetes: false,
      hadSugarProblems: false,
      isSugarDrugsConsumer: false,
      arterialPressure: 120,
      isArterialPressureDrugsConsumer: false,
      physicalActivityMinutes: 120,
      hadHeartAttackOrStroke: window.location.href.indexOf('hadHeartAttackOrStroke=1') >= 0,
      isAddingExtraSalt: false
    };
  },

  onAuthDone: function() {
    apiRequest('GET', '/api/v1/account', function(err, user) {
      if (err) {
        return this.setState({authError: err});
      }

      this.setState({user: user}, function() {
        this.openResultsStep();
      }.bind(this));
    }.bind(this));
  },

  openSecondStep: function() {
    if (this.isFormInvalid()) {
      return this.setState({
        formState: this.state.formState.markAll()
      });
    }

    if (this.props.user) {
      this.openResultsStep();
    } else {
      this.openAuthStep();
    }
  },

  openAuthStep: function() {
    this.setState({step: 'auth'});
  },

  openResultsStep: function() {
    this.setState({step: 'results', fetching: true});

    var data = {
      sex: this.state.sex,
      birthday: this.state.formState.data.birthday.format(),
      growth: this.state.formState.data.growth,
      weight: this.state.formState.data.weight,
      isSmoker: this.state.isSmoker,
      cholesterolLevel: this.state.cholesterolLevel,
      isCholesterolDrugsConsumer: this.state.cholesterolLevel >= 5 ? this.state.isCholesterolDrugsConsumer : null,
      isAcetylsalicylicDrugsConsumer: this.state.isAcetylsalicylicDrugsConsumer,
      hasDiabetes: this.state.hasDiabetes,
      hadSugarProblems: !this.state.hasDiabetes ? this.state.hadSugarProblems : null,
      isSugarDrugsConsumer: this.state.hasDiabetes ? this.state.isSugarDrugsConsumer : null,
      arterialPressure: this.state.arterialPressure,
      isArterialPressureDrugsConsumer: this.state.arterialPressure >= 140 ? this.state.isArterialPressureDrugsConsumer : null,
      physicalActivityMinutes: this.state.physicalActivityMinutes,
      hadHeartAttackOrStroke: this.state.hadHeartAttackOrStroke,
      isAddingExtraSalt: this.state.isAddingExtraSalt
    };

    apiRequest('POST', '/api/v1/account/test-results', data, function(err, results) {
      if (err) {
        return this.setState({error: err, fetching: false});
      }

      this.setState({results: results.data, fetching: false});
    }.bind(this));
  },

  renderResultsStep: function() {
    return (
      <div>
        <TestResult testResult={this.state.results} />
      </div>
    );
  },

  renderAuthStep: function() {
    return (
      <div>
        <div className="h h_2 h_center">Шаг 2. Авторизация</div>
        {this.state.authError &&
        <p>Ошибка авторизации</p>
        }
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
        <div className="h h_2 h_center h_button" onClick={this.openThirdStep}>Шаг 3. Регистрация<i className="icon icon-arr-down"></i></div>
      </div>
    );
  },

  renderFirstStep: function() {
    var stepTwoName = this.props.user ? 'Результаты' : 'Авторизация';

    return (
      <div>
        <div className="h h_2 h_center">Шаг 1. Данные пациента</div>
        <div className="l">
          <div className="l__column">
            <div className="fieldset fieldset_label-w60p">
              <div className="fieldset__title">Данные пациента</div>
              <div className="fieldset__in">
                <div className="field field_radio-row">
                  <div className="field__label">Пол</div>
                  <RadioGroup valueLink={this.linkState('sex')}
                              options={[{value: 'male', text: 'муж.'}, {value: 'female', text: 'жен.'}]} />
                </div>
                <div className={"field " + (this.isFieldErrorVisible('birthday') && 'is-error' || '')}>
                  <div className="field__label">Возраст</div>
                  <DateInput valueLink={this.linkForm('birthday')} />
                </div>
                <div className={"field " + (this.isFieldErrorVisible('growth') && 'is-error' || '')}>
                  <div className="field__label">Рост</div>
                  <div className="field__in">
                    <NumberInput valueLink={this.linkForm('growth')} />
                    <div className="field__info">см</div>
                  </div>
                </div>
                <div className={"field " + (this.isFieldErrorVisible('weight') && 'is-error' || '')}>
                  <div className="field__label">Вес<div className="field__error-text">Минимальный вес - 30 кг</div></div>
                  <div className="field__in">
                    <NumberInput valueLink={this.linkForm('weight')} />
                    <div className="field__info">кг</div>
                  </div>
                </div>
                <div className="field field_radio-row">
                  <div className="field__label">Вы курите?<div className="field__error-text">Выберите вариант</div></div>
                  <BooleanRadioGroup valueLink={this.linkState('isSmoker')} />
                </div>
                <div className="field">
                  <div className="field__label">Уровень общего холестирина</div>
                  <Range valueLink={this.linkState('cholesterolLevel')} min={3} max={9} step={0.5} />
                </div>
                {this.state.cholesterolLevel >= 5 &&
                <div className="field field_radio-row">
                  <div className="field__label">Принимаете лекарства для снижения уровня холестерина</div>
                  <BooleanRadioGroup valueLink={this.linkState('isCholesterolDrugsConsumer')} />
                </div>
                }
                <div className="field field_radio-row">
                  <div className="field__label">Принимаете ли припараты на основе ацетилсалициловой кислоты для профилактики риска тромбозов</div>
                  <BooleanRadioGroup valueLink={this.linkState('isAcetylsalicylicDrugsConsumer')} />
                </div>
              </div>
            </div>
          </div>
          <div className="l__column">
            <div className="fieldset fieldset_label-w65p">
              <div className="fieldset__title">Личные данные</div>
              <div className="fieldset__in">
                <div className="field field_radio-row">
                  <div className="field__label">Страдаете ли вы диабетом?</div>
                  <BooleanRadioGroup valueLink={this.linkState('hasDiabetes')} />
                </div>
                {!this.state.hasDiabetes &&
                <div className="field field_radio-row">
                  <div className="field__label">Отмечалось ли повышение уровня сахара в крови</div>
                  <BooleanRadioGroup valueLink={this.linkState('hadSugarProblems')} />
                </div>
                }
                {this.state.hasDiabetes &&
                <div className="field field_radio-row">
                  <div className="field__label">Принимаете ли препараты для контроля уровня сахара в крови</div>
                  <BooleanRadioGroup valueLink={this.linkState('isSugarDrugsConsumer')} />
                </div>
                }
                <div className="field field_rows">
                  <div className="field__label">Артериальное давление в мм рт. ст.</div>
                  <Range valueLink={this.linkState('arterialPressure')} min={80} max={200} />
                </div>
                {this.state.arterialPressure >= 140 &&
                <div className="field field_radio-row">
                  <div className="field__label">Принимаете ли препараты для понижения давления?</div>
                  <BooleanRadioGroup valueLink={this.linkState('isArterialPressureDrugsConsumer')} />
                </div>
                }
                <div className="field field_rows">
                  <div className="field__label">Сколько минут в неделю занимаетесь физической активностью</div>
                  <Range valueLink={this.linkState('physicalActivityMinutes')} min={80} max={200} revertBackgroud />
                </div>
                <div className="field field_radio-row">
                  <div className="field__label">Был ли у вас инфаркт/инсульт?</div>
                  <BooleanRadioGroup valueLink={this.linkState('hadHeartAttackOrStroke')} />
                </div>
                <div className="field field_radio-row">
                  <div className="field__label">Досаливаете ли вы пищу?</div>
                  <BooleanRadioGroup valueLink={this.linkState('isAddingExtraSalt')} />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="h h_2 h_center h_button" onClick={this.openSecondStep}>Шаг 2. {stepTwoName}<i className="icon icon-arr-down"></i></div>
      </div>
    );
  },

  render: function() {
    var content;
    if (this.state.error) {
      content = <p>{this.state.error.responseJSON ? this.state.error.responseJSON.errors[0].title : (this.state.error.message || this.state.error)}</p>;
    } else if (this.state.fetching) {
      content = <LargeSpin />;
    } else if (this.state.step == 'auth') {
      content = this.renderAuthStep();
    } else if (this.state.step == 'results') {
      content = this.renderResultsStep();
    } else {
      content = this.renderFirstStep();
    }

    return (
      <div className="container">
        <div className="head">
          <div className="head__right">
            <div className="head__city">
              <a href="#"><i className="icon icon-marker"></i><span>Москва</span></a>
            </div>
          </div>
          <div className="head__left">
            <div className="h">Анализ риска</div>
          </div>
        </div>
        {content}
      </div>
    );
  }
});

module.exports = RiskAnalysis;
