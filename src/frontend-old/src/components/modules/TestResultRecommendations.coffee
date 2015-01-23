`/** @jsx React.DOM */`

React = require "react"
$ = require "jquery"
cx = require "react/lib/cx"
LinkedStateMixin = require "../../mixins/LinkedStateMixin"
ValidationMixin = require "../../mixins/ValidationMixin"
validationConstraints = require "../../services/validationConstraints"
gradientCalculatorFactory = require "../../util/gradientCalculatorFactory"
Input = require "../registration/Input"


TestResultRecommendations = React.createClass
  mixins: [LinkedStateMixin, ValidationMixin]

  statics:
    maleMaxScoreValue: 47
    femaleMaxScoreValue: 20
    additionalDietBanner:
      pageUrl: '/'
      state: 'ask'
      title: 'Дополнительная корректировка диеты'
      subtitle: null
      note: 'Пройти опрос'

  getDefaultProps: ->
    sex: "male"
    scoreValue: "47"

  getInitialState: ->
    recommendations: if typeof @props.recommendations is "string" then window[@props.recommendations] else @props.recommendations
    popupActive: false
    email: ''
    showErrors: false
    popupStatus: null

  getValidationConfig: ->
    children:
      email:
        notEmpty: validationConstraints.notEmpty()
        email: validationConstraints.email()
        
  componentDidMount: ->
    @animated = false
    @animate()
      
  componentDidUpdate: ->
    @animate()

  animate: ->
    return if not @props.recommendations or @animated

    @animated = true
    
    pageNode = @getDOMNode()
    scoreValueNode = @refs.scoreValue.getDOMNode()
    scoreValueTextNode = @refs.scoreValueText.getDOMNode()
    
    scoreValue = Number @props.scoreValue
    
    scoreValuePercent = if @props.sex is "male"
      TestResultRecommendations.maleMaxScoreValue / 100
    else
      TestResultRecommendations.femaleMaxScoreValue / 100

    maximumGradientTime = if @state.recommendations.fullScreenAlert
      1
    else
      scoreValue / scoreValuePercent / 100

    animationDuration = 1000
    frameDuration = 1000 / 60
    framesLength = animationDuration / frameDuration
    frameGradientTime = maximumGradientTime / framesLength
    
    time = 0
    gradientCalculator = gradientCalculatorFactory [241, 65, 67], [85, 189, 230]

    nextColor = ->
      time = 1 if time > 1
      color = gradientCalculator time
      color = "rgb(#{color[0]}, #{color[1]}, #{color[2]})"

      pageNode.style.backgroundColor = color
      
      currentScoreValue = Math.ceil scoreValuePercent * time * 100
      if currentScoreValue <= scoreValue
        scoreValueNode.style.left = (time * 100) + '%'
        scoreValueTextNode.style.color = color
        scoreValueTextNode.textContent = currentScoreValue

      return if time > maximumGradientTime
      return if time is 1
      time += frameGradientTime
      setTimeout nextColor, frameDuration

    nextColor()


  componentWillReceiveProps: (newProps) ->
    @setState recommendations: if typeof newProps.recommendations is "string" then window[newProps.recommendations] else newProps.recommendations
    
  togglePopup: ->
    @setState
      popupActive: not @state.popupActive
      popupStatus: null
    
  submitEmail: ->
    if @validity.invalid
      return @setState showErrors: true

    $.ajax
      cache: false
      contentType: "application/json; charset=utf-8"
      data: JSON.stringify(email: @state.email)
      dataType: "text"
      success: @handleRequest
      error: @handleError
      type: "POST"
      url: "/api/test-results/#{@props.testResultId}/send-email"

  handleRequest: (data) ->
    @setState
      email: ""
      popupStatus: "Сообщение успешно отправлено"
      
    setTimeout =>
      @setState popupActive: false
    , 2000

  handleError: (xhr, testStatus, errorThrown) ->
    @setState
      popupStatus: "Ошибка отправки сообщения"

  render: ->
    if not @state.recommendations
      return `(<div />)`
    
    maxScoreValue = if @props.sex is "male" then TestResultRecommendations.maleMaxScoreValue else TestResultRecommendations.femaleMaxScoreValue
    scoreOffset = Number(@props.scoreValue) / (maxScoreValue / 100)
    
    scoreNote = if @state.recommendations.scoreNote
      `(
        <div className="result__text">
          <i className={'result__' + this.state.recommendations.scoreNote.state}></i>
          <div dangerouslySetInnerHTML={{__html: this.state.recommendations.scoreNote.text.replace(/\n/g, '<br />')}} />
        </div>
      )`
    else
      null

    fullScreenAlert = if @state.recommendations.fullScreenAlert
      `(
        <div className="result__text">
          <i className={'result__' + this.state.recommendations.fullScreenAlert.state}></i>
          <div dangerouslySetInnerHTML={{__html: this.state.recommendations.fullScreenAlert.text.replace(/\n/g, '<br />')}} />
        </div>
      )`
    else
      null

    mainRecommendation = if @state.recommendations.mainRecommendation and not fullScreenAlert
      `(
        <div className="result__text">
          <div dangerouslySetInnerHTML={{__html: this.state.recommendations.mainRecommendation.text.replace(/\n/g, '<br />')}} />
        </div>
      )`
    else
      null

    banners = if not fullScreenAlert
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
      #"page_step_3": true
      #"is-red": not not fullScreenAlert
      
    emailPopupClasses = cx
      "result__send": true
      "is-active": @state.popupActive
      
    popup = if @state.popupStatus
      `(
				<div className={emailPopupClasses}>
					<i className="result__send-ico" onClick={this.togglePopup}></i>
					<div className="result__send-in" style={{display: this.state.popupActive ? 'block' : 'none'}}>
						{this.state.popupStatus}
					</div>
				</div>
		  )`
    else
      `(
				<div className={emailPopupClasses}>
					<i className="result__send-ico" onClick={this.togglePopup}></i>
					<div className="result__send-in" style={{display: this.state.popupActive ? 'block' : 'none'}}>
						<div className="field">
							<div className="field__label">Отправить результат тестирования по электронной почте</div>
							<div className="field__in">
								<Input placeholder="email получателя"
								       valueLink={this.linkState('email')}
								       invalid={this.state.showErrors && this.validity.children.email.invalid}
								       invalidMessage="Неправильный адрес" />
							</div>
						</div>
						<button className="btn" onClick={this.submitEmail}>Отправить</button>
					</div>
				</div>
		  )`
 
    `(
      <div className={classes}>
        <div className="result">
          <div className="result__top">
            {popup}
  					<div className="help help_white" style={{display: 'none'}}>
  						<div className="help__ico"></div>
  					</div>
  				</div>
          <div className="result__scale">
  					<div className="result__val">
  						<div className="result__val-in" ref="scoreValue">
  							<span ref="scoreValueText">0</span>
  						</div>
  					</div>
  					<div className="result__line"><i></i></div>
  				</div>
          {scoreNote}
          {fullScreenAlert}
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
      
    subtitle = if @props.banner.subtitle
      `(<p>Отклонение от нормы: {this.props.banner.subtitle}</p>)`
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
            {subtitle}
            {note}
          </div>
        </div>
      </Container>
    )`


module.exports = TestResultRecommendations
