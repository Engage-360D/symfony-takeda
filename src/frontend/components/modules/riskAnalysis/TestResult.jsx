var React = require('react');

var TestResult = React.createClass({
  render: function() {
    return (
      <div className="result">
        <div className="result__top">
          <div className="result__send is-active">
            <i className="icon icon-mail-circle-fill"></i>
            <div className="result__send-in">
              <div className="field field_rows">
                <div className="field__label">Отправить результат тестирования по электронной почте</div>
                <div className="field__in">
                  <input className="input" type="text" placeholder="email получателя" />
                </div>
              </div>
              <button className="button">Отправить</button>
            </div>
          </div>
          <div className="result__help">
            <i className="icon icon-info-fill"></i>
          </div>
        </div>
        <div className="result__scale">
          <div className="result__val">
            <div className="result__val-in" style={{left: '32%'}}>
              <span>32</span>
            </div>
          </div>
          <div className="result__line"><i></i></div>
        </div>
        <div className="result__diagnosis">Вероятность тяжелых сердечно-сосудистых заболеваний в ближайшие 10 лет</div>
        <div className="result__text">
          <div className="result__text-item">
            <div className="result__text-ico">
              <i className="icon icon-info-triangle-big"></i>
            </div>
            <div className="result__text-in">Прием препаратов ацетилсалициловой кислоты является фактором риска развития язвенной болезни желудка. Обратите внимание на специализированные препараты, снижающие этот риск, например, кардиомагнил.</div>
          </div>
          <div className="result__text-item">
            <div className="result__text-ico">
              <i className="icon icon-bell-big"></i>
            </div>
            <div className="result__text-in">Прием препаратов ацетилсалициловой кислоты является фактором риска развития язвенной болезни желудка. Обратите внимание на специализированные препараты, снижающие этот риск, например, кардиомагнил.</div>
          </div>
          <div className="result__text-item">
            <div className="result__text-ico">
              <i className="icon icon-ask-circle-big"></i>
            </div>
            <div className="result__text-in">Прием препаратов ацетилсалициловой кислоты является фактором риска развития язвенной болезни желудка. Обратите внимание на специализированные препараты, снижающие этот риск, например, кардиомагнил.</div>
          </div>
          <div className="result__text-item">
            <div className="result__text-ico">
              <i className="icon icon-doc"></i>
            </div>
            <div className="result__text-in">Прием препаратов ацетилсалициловой кислоты является фактором риска развития язвенной болезни желудка. Обратите внимание на специализированные препараты, снижающие этот риск, например, кардиомагнил.</div>
          </div>
          <div className="result__text-item">
            <div className="result__text-ico">
              <i className="icon icon-ok-circle-big"></i>
            </div>
            <div className="result__text-in">Прием препаратов ацетилсалициловой кислоты является фактором риска развития язвенной болезни желудка. Обратите внимание на специализированные препараты, снижающие этот риск, например, кардиомагнил.</div>
          </div>
        </div>
        <div className="l">
          <div className="l__column">
            <div className="recomm">
              <div className="recomm__item">
                <div className="recomm__title">Курение</div>
                <div className="recomm__content">
                  <div className="recomm__ico">
                    <i className="icon icon-info-triangle"></i>
                  </div>
                  <div className="recomm__description"><span>Группа риска</span></div>
                </div>
              </div>
              <div className="recomm__item">
                <div className="recomm__title">Физическая активность</div>
                <a className="recomm__content" href="#">
                  <div className="recomm__ico">
                    <i className="icon icon-bell"></i>
                  </div>
                  <div className="recomm__description"><span>Физическая активность</span>Отклонение от нормы 10%<br />Необходимо улучшение</div>
                  <i className="icon icon-arr-right"></i>
                </a>
                <a className="recomm__content" href="#">
                  <div className="recomm__ico">
                    <i className="icon icon-ok-circle"></i>
                  </div>
                  <div className="recomm__description"><span>Вес</span>Отклонение от нормы: 2%<br />Все хорошо</div>
                  <i className="icon icon-arr-right"></i>
                </a>
              </div>
              <div className="recomm__item">
                <div className="recomm__title">Диета</div>
                <a className="recomm__content" href="#">
                  <div className="recomm__ico">
                    <i className="icon icon-bell"></i>
                  </div>
                  <div className="recomm__description"><span>Потребление соли</span>Отклонение от нормы 10%<br />Необходимо улучшение</div>
                  <i className="icon icon-arr-right"></i>
                </a>
                <a className="recomm__content" href="#">
                  <div className="recomm__ico">
                    <i className="icon icon-ask-circle"></i>
                  </div>
                  <div className="recomm__description"><span>Дополнительная корректировка диеты</span>Пройти опрос</div>
                  <i className="icon icon-arr-right"></i>
                </a>
              </div>
            </div>
          </div>
          <div className="l__column">
            <div className="recomm">
              <div className="recomm__item">
                <div className="recomm__title">Основные риски</div>
                <a className="recomm__content" href="#">
                  <div className="recomm__ico">
                    <i className="icon icon-bell"></i>
                  </div>
                  <div className="recomm__description"><span>Уровень холестирина</span>Отклонение от нормы 10%<br />Необходимо улучшение</div>
                </a>
                <a className="recomm__content" href="#">
                  <div className="recomm__ico">
                    <i className="icon icon-info-triangle"></i>
                  </div>
                  <div className="recomm__description"><span>Физическая активность</span>Отклонение от нормы 10%<br />Необходимо улучшение</div>
                  <i className="icon icon-arr-right"></i>
                </a>
                <a className="recomm__content" href="#">
                  <div className="recomm__ico">
                    <i className="icon icon-doc"></i>
                  </div>
                  <div className="recomm__description"><span>Вес</span>Отклонение от нормы: 2%<br />Все хорошо</div>
                  <i className="icon icon-arr-right"></i>
                </a>
                <div className="recomm__content">
                  <div className="recomm__ico">
                    <i className="icon icon-doc"></i>
                  </div>
                  <div className="recomm__description"><span>Потребление соли</span>Отклонение от нормы 10%<br />Необходимо улучшение</div>
                </div>
                <div className="recomm__content">
                  <div className="recomm__ico">
                    <i className="icon icon-doc"></i>
                  </div>
                  <div className="recomm__description"><span>Дополнительная корректировка диеты</span>Пройти опрос</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }
});

module.exports = TestResult;
