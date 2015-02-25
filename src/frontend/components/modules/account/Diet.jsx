var React = require('react');
var $ = require('jquery');

var Diet = React.createClass({
  getInitialState: function() {
    return {
      selected: this.props.questions.reduce(function(selected, question) {
        selected[question.id] = this.props.answers[0].id;
        return selected;
      }.bind(this), {}),
      result: null
    };
  },

  handleClick: function() {
    $.ajax({
      type: 'GET',
      dataType: 'json',
      data: {
        answers: this.state.selected
      },
      url: '/api/v1/account/test-results/'+this.props.testResultId+'/diet-recommendations',
      success: function(data) {
        this.setState({result: data.data});
      }.bind(this)
    })
  },

  renderBlocks: function() {
    var red, blue;

    //if (this.state.result.red.length > 0) {
      red = (
          <div className="recomm__column">
            <div className="h h_2">Следует предпочесть</div>
            <div className="recomm__item">
              {this.state.result.red.map(function(banner, index) {
                return (
                  <div className="recomm__content" key={index}>
                    <div className="recomm__ico">
                      <i className="icon icon-info-triangle"></i>
                    </div>
                    <div className="recomm__description"><span>{banner.title}</span><br />{banner.note}</div>
                  </div>
                );
              })}
            </div>
          </div>
      );
    //}

    //if (this.state.result.blue.length > 0) {
      blue = (
          <div className="recomm__column">
            <div className="h h_2">Следует употреблять умеренно</div>
            <div className="recomm__item">
              {this.state.result.blue.map(function(banner, index) {
                return (
                  <div className="recomm__content" key={index}>
                    <div className="recomm__description">{banner.text}</div>
                  </div>
                );
              })}
            </div>
          </div>
      );
    //}

    if (!red && !blue) return;

    return (
      <div className="recomm recomm_l">
        <div className="recomm__table">
          {red}
          {blue}
        </div>
      </div>
    );
  },

  renderQuestion: function(question, index) {
    return (
      <div className="field field_rows field_radio-three" key={index}>
        <div className="field__label">{question.question}</div>
        <div className="field__in">
          {this.props.answers.map(function(answer, index) {
            var checkedLink = {
              value: this.state.selected[question.id] === answer.id,
              requestChange: function() {
                this.state.selected[question.id] = answer.id;
                this.state.result = null;
                this.forceUpdate();
              }.bind(this)
            };

            return (
              <label className="field__radio" key={index} style={{width: index === 1 ? '36%' : '32%'}}>
                <input type="radio" checkedLink={checkedLink} />
                <span>{answer.answer}</span>
              </label>
            );
          }.bind(this))}
        </div>
      </div>
    );
  },

  renderQuestions: function(questions) {
    return questions.map(this.renderQuestion);
  },

  renderResult: function() {
    return (
      <div className="content content_mb70">
        {this.state.result.messages.map(function(msg, i) { return <p key={i}>{msg}</p>; })}
      </div>
    );
  },

  render: function() {
    var left = [], right = [];

    this.props.questions.forEach(function(question, index) {
      if (index % 2 === 0) {
        left.push(question);
      } else {
        right.push(question);
      }
    });

    return (
      <div>
        <div className="l">
          <div className="l__column">
            <div className="fieldset fieldset_item-border">
              <div className="fieldset__in">
                {this.renderQuestions(left)}
              </div>
            </div>
          </div>
          <div className="l__column">
            <div className="fieldset fieldset_item-border">
              <div className="fieldset__in">
                {this.renderQuestions(right)}
              </div>
            </div>
          </div>
        </div>
        <div className="h h_2 h_center h_button" onClick={this.handleClick}>Диета и рацион<i className="icon icon-arr-down"></i></div>
        {this.state.result && this.renderResult()}
        {this.state.result && this.renderBlocks()}
      </div>
    );
  }
});

module.exports = Diet;
