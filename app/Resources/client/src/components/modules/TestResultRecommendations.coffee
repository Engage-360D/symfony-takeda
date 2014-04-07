`/** @jsx React.DOM */`

React = require "react"


TestResultRecommendations = React.createClass
  getDefaultProps: ->
    sex: "male"
    scoreValue: "47"

  getInitialState: ->
    recommendations: if typeof @props.recommendations is "string" then window[@props.recommendations] else @props.recommendations

  componentWillReceiveProps: (newProps) ->
    @setState recommendations: if typeof newProps.recommendations is "string" then window[newProps.recommendations] else newProps.recommendations

  render: ->
    maxScoreValue = if @props.sex is "male" then 47 else 20
    scoreOffset = Number(@props.scoreValue) / (maxScoreValue / 100)

    `(
      <div className="page page_step_3">
        <div className="result">
          <div className="result__top">
            <div className="result__info"></div>
            <div className="result__arrow"></div>
          </div>
          <div className="result__val">
            <div className="result__val-blue" style={{width: scoreOffset + '%'}}><span>{this.props.scoreValue}</span></div>
            <div className="result__val-red"></div>
          </div>
          <div className="result__text">Вероятность тяжелых сердечно-сосудистых заболеваний в ближайшие 10 лет</div>
          <div className="result__text" style={{display: 'none'}}><i className="result__attention-big"></i>Не соответствует норме. Воспользуйтесь нашими рекомендациями</div>
          <div className="layout">
            <div className="layout__column">
              <div className="recomm">
                <div className="recomm__item">
                  <div className="recomm__title">Физическая активность</div>
                  <a className="recomm__content" href={this.state.recommendations ? this.state.recommendations.physicalActivity.url : ''}>
                    <i className={'recomm__' + (this.state.recommendations ? this.state.recommendations.physicalActivity.state : '')}></i>
                    <div className="recomm__item-sub">
                      <div className="recomm__title-sub">Физическая активность</div>
                      <div class="recomm__text">
                        <p>Отклонение от нормы: {this.state.recommendations ? this.state.recommendations.physicalActivity.aberration : ''}</p>
                        <p>{this.state.recommendations ? this.state.recommendations.physicalActivity.stateTitle : ''}</p>
                      </div>
                    </div>
                  </a>
                  <a className="recomm__content" href={this.state.recommendations ? this.state.recommendations.bmi.url : ''}>
                    <i className={'recomm__' + (this.state.recommendations ? this.state.recommendations.bmi.state : '')}></i>
                    <div className="recomm__item-sub">
                      <div className="recomm__title-sub">Вес</div>
                      <div class="recomm__text">
                        <p>Отклонение от нормы: {this.state.recommendations ? this.state.recommendations.bmi.aberration : ''}</p>
                        <p>{this.state.recommendations ? this.state.recommendations.bmi.stateTitle : ''}</p>
                      </div>
                    </div>
                  </a>
                </div>
                <div className="recomm__item">
                  <div className="recomm__title">Диета</div>
                  <a className="recomm__content" href={this.state.recommendations ? this.state.recommendations.extraSalt.url : ''}>
                    <i className={'recomm__' + (this.state.recommendations ? this.state.recommendations.extraSalt.state : '')}></i>
                    <div className="recomm__item-sub">
                      <div className="recomm__title-sub">Потребление соли</div>
                      <div class="recomm__text">
                        <p>Отклонение от нормы: {this.state.recommendations ? this.state.recommendations.extraSalt.aberration : ''}</p>
                        <p>{this.state.recommendations ? this.state.recommendations.extraSalt.stateTitle : ''}</p>
                      </div>
                    </div>
                  </a>
                  <a className="recomm__content" href="/ration-test" style={{display: 'none'}}>
                    <i className={'recomm__ask'}></i>
                    <div className="recomm__item-sub">
                      <div className="recomm__title-sub">Дополнительная корректировка диеты</div>
                      <div class="recomm__text">
                        <p>Пройти опрос</p>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
            <div className="layout__column">
              <div className="recomm">
                <div className="recomm__item">
                  <div className="recomm__title">Курение</div>
                  <a className="recomm__content" href={this.state.recommendations ? this.state.recommendations.smoking.url : ''}>
                    <i className={'recomm__' + (this.state.recommendations ? this.state.recommendations.smoking.state : '')}></i>
                    <div className="recomm__item-sub">
                      <div className="recomm__title-sub">{this.state.recommendations ? this.state.recommendations.smoking.stateTitle : ''}</div>
                    </div>
                  </a>
                </div>
                <div className="recomm__item">
                  <div className="recomm__title">Основные риски</div>
                  <a className="recomm__content" href={this.state.recommendations ? this.state.recommendations.cholesterolLevel.url : ''}>
                    <i className={'recomm__' + (this.state.recommendations ? this.state.recommendations.cholesterolLevel.state : '')}></i>
                    <div className="recomm__item-sub">
                      <div className="recomm__title-sub">Уровень холестирина</div>
                      <div class="recomm__text">
                        <p>Отклонение от нормы: {this.state.recommendations ? this.state.recommendations.cholesterolLevel.aberration : ''}</p>
                        <p>{this.state.recommendations ? this.state.recommendations.cholesterolLevel.stateTitle : ''}</p>
                      </div>
                    </div>
                  </a>
                  <a className="recomm__content" href={this.state.recommendations ? this.state.recommendations.arterialPressure.url : ''}>
                    <i className={'recomm__' + (this.state.recommendations ? this.state.recommendations.arterialPressure.state : '')}></i>
                    <div className="recomm__item-sub">
                      <div className="recomm__title-sub">Систолическое давление</div>
                      <div class="recomm__text">
                        <p>Отклонение от нормы: {this.state.recommendations ? this.state.recommendations.arterialPressure.aberration : ''}</p>
                        <p>{this.state.recommendations ? this.state.recommendations.arterialPressure.stateTitle : ''}</p>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    )`


module.exports = TestResultRecommendations
