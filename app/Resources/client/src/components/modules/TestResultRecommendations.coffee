`/** @jsx React.DOM */`

React = require "react"
cx = require "react/lib/cx"


TestResultRecommendations = React.createClass
  statics:
    additionalDietBanner:
      pageUrl: '/'
      state: 'ask'
      title: 'Дополнительная корректировка диеты'
      aberration: null
      note: 'Пройти опрос'

  getDefaultProps: ->
    sex: "male"
    scoreValue: "47"

  getInitialState: ->
    recommendations: if typeof @props.recommendations is "string" then window[@props.recommendations] else @props.recommendations

  componentWillReceiveProps: (newProps) ->
    @setState recommendations: if typeof newProps.recommendations is "string" then window[newProps.recommendations] else newProps.recommendations

  render: ->
    if not @state.recommendations
      return `(<div />)`
    
    maxScoreValue = if @props.sex is "male" then 47 else 20
    scoreOffset = Number(@props.scoreValue) / (maxScoreValue / 100)
    
    scoreDescription = if @state.recommendations.scoreDescription
      `(
        <div className="result__text">
          <i className={'result__' + this.state.recommendations.scoreDescription.state}></i>
          <div dangerouslySetInnerHTML={{__html: this.state.recommendations.scoreDescription.text.replace(/\n/g, '<br />')}} />
        </div>
      )`
    else
      null

    dangerAlert = if @state.recommendations.dangerAlert
      `(
        <div className="result__text">
          <i className={'result__' + this.state.recommendations.dangerAlert.state}></i>
          <div dangerouslySetInnerHTML={{__html: this.state.recommendations.dangerAlert.text.replace(/\n/g, '<br />')}} />
        </div>
      )`
    else
      null

    mainRecommendation = if @state.recommendations.mainRecommendation and not dangerAlert
      `(
        <div className="result__text">
          <div dangerouslySetInnerHTML={{__html: this.state.recommendations.mainRecommendation.text.replace(/\n/g, '<br />')}} />
        </div>
      )`
    else
      null

    banners = if not dangerAlert
      `(
        <div className="layout">
          <div className="layout__column">
            <div className="recomm">
              <div className="recomm__item">
                <div className="recomm__title">Физическая активность</div>
                <TestResultRecommendationsBanner banner={this.state.recommendations.banners.physicalActivity} />
                <TestResultRecommendationsBanner banner={this.state.recommendations.banners.bmi} />
              </div>
              <div className="recomm__item">
                <div className="recomm__title">Диета</div>
                <TestResultRecommendationsBanner banner={this.state.recommendations.banners.extraSalt} />
                <TestResultRecommendationsBanner banner={TestResultRecommendations.additionalDietBanner} />
              </div>
            </div>
          </div>
          <div className="layout__column">
            <div className="recomm">
              <div className="recomm__item">
                <div className="recomm__title">Курение</div>
                <TestResultRecommendationsBanner banner={this.state.recommendations.banners.smoking} />
              </div>
              <div className="recomm__item">
                <div className="recomm__title">Основные риски</div>
                <TestResultRecommendationsBanner banner={this.state.recommendations.banners.cholesterolLevel} />
                <TestResultRecommendationsBanner banner={this.state.recommendations.banners.arterialPressure} />
                <TestResultRecommendationsBanner banner={this.state.recommendations.banners.sugarProblems} />
                <TestResultRecommendationsBanner banner={this.state.recommendations.banners.arterialPressureDrugs} />
                <TestResultRecommendationsBanner banner={this.state.recommendations.banners.cholesterolDrugs} />
              </div>
            </div>
          </div>
        </div>
      )`
    else
      null
      
    classes = cx
      "page": true
      "page_step_3": true
      "is-red": not not dangerAlert

    `(
      <div className={classes}>
        <div className="result">
          <div className="result__top">
            <div className="result__info"></div>
            <div className="result__arrow"></div>
          </div>
          <div className="result__scale">
  					<div className="result__val">
  						<div className="result__val-in" style={{left: scoreOffset + '%'}}>
  							<span>{this.props.scoreValue}</span>
  						</div>
  					</div>
  					<div className="result__line"><i></i></div>
  				</div>
          {scoreDescription}
          {dangerAlert}
          {mainRecommendation}
          {banners}
        </div>
      </div>
    )`


TestResultRecommendationsBanner = React.createClass
  render: ->
    if not @props.banner
      return `(<div />)`
      
    title = if @props.banner.title
      `(<div className="recomm__title-sub">{this.props.banner.title}</div>)`
    else
      null
      
    aberration = if @props.banner.aberration
      `(<p>Отклонение от нормы: {this.props.banner.aberration}</p>)`
    else
      null
      
    note = if @props.banner.note
      `(<p>{this.props.banner.note}</p>)`
    else
      null

    Container = if @props.banner.pageUrl
      React.DOM.a
    else
      React.DOM.div

    `(
      <Container className="recomm__content" href={this.props.banner.pageUrl}>
        <i className={'recomm__' + this.props.banner.state}></i>
        <div className="recomm__item-sub">
          {title}
          <div className="recomm__text">
            {aberration}
            {note}
          </div>
        </div>
      </Container>
    )`


module.exports = TestResultRecommendations
