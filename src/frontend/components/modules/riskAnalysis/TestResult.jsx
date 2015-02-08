var React = require('react');
var apiRequest = require('../../../utilities/apiRequest');
var LinkedStateMixin = require('react/lib/LinkedStateMixin');

function bigIcon(state) {
  if (state === 'ok') {
    return <i className="icon icon-ok-circle-big"></i>;
  } else if (state === 'bell') {
    return <i className="icon icon-bell-big"></i>;
  } else if (state === 'attention') {
    return <i className="icon icon-info-triangle-big"></i>;
  } else if (state === 'doctor') {
    return <i className="icon icon-doc-big"></i>;
  } else {
    return null;
  }
}

function icon(state) {
  if (state === 'ok') {
    return <i className="icon icon-ok-circle"></i>;
  } else if (state === 'bell') {
    return <i className="icon icon-bell"></i>;
  } else if (state === 'attention') {
    return <i className="icon icon-info-triangle"></i>;
  } else if (state === 'doctor') {
    return <i className="icon icon-doc"></i>;
  } else if (state === 'ask') {
    return <i className="icon icon-ask-circle"></i>;
  } else {
    return null;
  }
}

function banner(banner) {
  if (!banner) {
    return null;
  }

  if (banner.pageUrl) {
    return (
      <a className="recomm__content" href={banner.pageUrl}>
        <div className="recomm__ico">
          {icon(banner.state)}
        </div>
        <div className="recomm__description"><span>{banner.title}</span>{banner.subtitle}<br />{banner.note}</div>
        <i className="icon icon-arr-right" />
      </a>
    );
  } else {
    return (
      <div className="recomm__content">
        <div className="recomm__ico">
          {icon(banner.state)}
        </div>
        <div className="recomm__description"><span>{banner.title}</span>{banner.subtitle}<br />{banner.note}</div>
      </div>
    );
  }
}

var TestResult = React.createClass({
  mixins: [LinkedStateMixin],

  getInitialState: function() {
    return {
      mailPopupOpened: false,
      mail: ''
    };
  },

  openMailPopup: function() {
    this.setState({mailPopupOpened: true});
  },

  sendEmail: function() {
    apiRequest('POST', '/api/v1/account/test-results/'+this.props.testResult.id+'/send-email', {
      email: this.state.mail
    }, function(err, data) {
      if (err) {
        return alert('Ошибка отправки сообщения. Пожалуйста, попробуйте еще раз через некоторое время.');
      }

      alert('Сообщение успешно отправлено.');
      this.setState({mail: '', mailPopupOpened: false});
    }.bind(this));
  },

  render: function() {
    var testResult = this.props.testResult;
    var recommendations = testResult.recommendations;
    var maxScoreValue = testResult.sex === 'male' ? 47 : 20;
    var scoreOffset = testResult.score / (maxScoreValue / 100);
    var disabled = !/^.+@.+\..{2,}$/.test(this.state.mail);

    return (
      <div className="result">
        <div className="result__top">
          <div className="result__send is-active">
            <i className="icon icon-mail-circle-fill" onClick={this.openMailPopup}></i>
            <div className="result__send-in" style={{display: this.state.mailPopupOpened ? 'block' : 'none'}}>
              <div className="field field_rows">
                <div className="field__label">Отправить результат тестирования по электронной почте</div>
                <div className="field__in">
                  <input className="input" type="text" placeholder="email получателя" valueLink={this.linkState('mail')} />
                </div>
              </div>
              <button className="button" disabled={disabled} onClick={this.sendEmail}>Отправить</button>
            </div>
          </div>
          <div className="result__help">
            <i className="icon icon-info-fill"></i>
          </div>
        </div>
        <div className="result__scale">
          <div className="result__val">
            <div className="result__val-in" style={{left: scoreOffset + '%'}}>
              <span>{testResult.score}</span>
            </div>
          </div>
          <div className="result__line"><i></i></div>
        </div>
        {/*
        <div className="result__diagnosis">Вероятность тяжелых сердечно-сосудистых заболеваний в ближайшие 10 лет</div>
        */}
        <div className="result__text" style={{marginTop: 30}}>
          <div className="result__text-item">
            <div className="result__text-ico">
              {bigIcon(recommendations.scoreNote.state)}
            </div>
            <div className="result__text-in">{recommendations.scoreNote.text}</div>
          </div>
        </div>
        <div className="result__text">
          <div className="result__text-item">
            <div className="result__text-in">
              <div style={{width: 750}}>{recommendations.mainRecommendation.text}</div>
            </div>
          </div>
        </div>
        <div className="l">
          <div className="l__column">
            <div className="recomm">
              <div className="recomm__item">
                <div className="recomm__title">Физическая активность</div>
                {banner(recommendations.banners.physicalActivityMinutes)}
                {banner(recommendations.banners.bmi)}
              </div>
              <div className="recomm__item">
                <div className="recomm__title">Диета</div>
                {banner(recommendations.banners.isAddingExtraSalt)}
                {banner({
                  pageUrl: '/',
                  state: 'ask',
                  title: 'Дополнительная корректировка диеты',
                  subtitle: '',
                  note: 'Пройти опрос'
                })}
              </div>
            </div>
          </div>
          <div className="l__column">
            <div className="recomm">
              <div className="recomm__item">
                <div className="recomm__title">Курение</div>
                {banner(recommendations.banners.isSmoker)}
              </div>
              <div className="recomm__item">
                <div className="recomm__title">Основные риски</div>
                {banner(recommendations.banners.cholesterolLevel)}
                {banner(recommendations.banners.arterialPressure)}
                {banner(recommendations.banners.hadSugarProblems)}
                {banner(recommendations.banners.isArterialPressureDrugsConsumer)}
                {banner(recommendations.banners.isCholesterolDrugsConsumer)}
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }
});

module.exports = TestResult;
