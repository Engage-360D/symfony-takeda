/**
 * cardiomagnyl
 */

/*jslint node: true*/

"use strict";

var React = require('react/addons');
var cx = React.addons.classSet;
var moment = require('moment');
var LinkedStateMixin = require('react/lib/LinkedStateMixin');
var apiRequest = require('../../../utilities/apiRequest');

var Report = React.createClass({

  mixins: [LinkedStateMixin],

  statics: {
    PERIOD_FORMAT_WEEK: 'W',

    PERIOD_MONTH: 'MONTH',
    PERIOD_QUARTER: 'QUARTER',
    PERIOD_YEAR: 'YEAR',

    COLOR_RED: '#FF3442',
    COLOR_BLUE: '#43BAE4',
    COLOR_GREY: '#E7E7E7',

    getColor: function (isDynamicPositive) {
      var color;
      if (isDynamicPositive === undefined) {
        color = Report.COLOR_GREY;
      } else if (isDynamicPositive) {
        color = Report.COLOR_BLUE;
      } else {
        color = Report.COLOR_RED;
      }
      return color;
    }
  },

  getInitialState: function () {
    return {
      currentPage: 0,
      currentPeriod: this.props.report.periodFormat === Report.PERIOD_FORMAT_WEEK ?
          Report.PERIOD_MONTH : Report.PERIOD_QUARTER,
      mail: '',
      mailPopupOpened: false
    };
  },

  componentDidMount: function () {
    google.setOnLoadCallback(this.drawChart);
  },

  getChartData: function (currentPage, currentPeriod) {
    var data = [["Month", "Value", { role: "style" }]];
    var startDate;
    var endDate;
    var currentDate;
    var currentPage = currentPage || this.state.currentPage;
    var currentPeriod = currentPeriod || this.state.currentPeriod;
    var reportData = this.props.report.data;
    var i;

    switch (currentPeriod) {
      case Report.PERIOD_MONTH:
        startDate = moment().startOf('month').add(currentPage, 'month');
        endDate = startDate.clone().add(1, 'month');
        break;
      case Report.PERIOD_QUARTER:
        startDate = moment().startOf('quarter').add(currentPage, 'quarter');
        endDate = startDate.clone().add(1, 'quarter');
        break;
      case Report.PERIOD_YEAR:
        startDate = moment().startOf('year').add(currentPage, 'year');
        endDate = startDate.clone().add(1, 'year');
        break;
    }

    for (i = 0; i < reportData.length; i++) {
      currentDate = moment(reportData[i].date).add(1, 'second');
      if (currentDate.isBetween(startDate, endDate, 'second')) {
        data.push([
          currentDate.format(this.props.report.periodFormat),
          Math.round(Number(reportData[i].value)),
          Report.getColor(reportData[i].isDynamicPositive)
        ]);
      }
    }

    return data;
  },

  drawChart: function () {
    if (this.props.report.data.length === 0) {
      return;
    }
    var data = this.getChartData();
    var view = new google.visualization.DataView(
      google.visualization.arrayToDataTable(data)
    );
    view.setColumns([
      0,
      1,
      {
        calc: "stringify",
        sourceColumn: 1,
        type: "string",
        role: "annotation"
      },
      2
    ]);
    var options = {
      width: 720,
      legend: {position: 'none'},
      chartArea: {
        width: '90%',
        left: '5%'
      },
      tooltip: {trigger: 'none'},
      bar: { groupWidth: "96%" },
      hAxis: {
        title: "Месяц"
      },
      vAxis: {
        minValue: 0
      }
    };
    if (this.props.report.periodFormat === Report.PERIOD_FORMAT_WEEK) {
      options.vAxis.maxValue = 100;
      options.hAxis.title = "Неделя";
    }
    new google
      .visualization
      .ColumnChart(document.getElementById('google-chart'))
      .draw(view, options);
  },

  handlePrintButtonClick: function (event) {
    event.preventDefault();
    window.print();
  },

  handleGoToDoctorLinkClick: function (event) {
    event.preventDefault();
    window.location = this.props.institutionsLink;
  },

  handleNextButtonClick: function (event) {
    event.preventDefault();
    this.setState({
      currentPage: this.state.currentPage + 1
    }, function () {
      this.drawChart();
    }.bind(this));
  },

  handlePrevButtonClick: function (event) {
    event.preventDefault();
    this.setState({
      currentPage: this.state.currentPage - 1
    }, function () {
      this.drawChart();
    }.bind(this));
  },

  handlePeriodButtonClick: function (newPeriod, event) {
    event.preventDefault();
    this.setState({
      currentPeriod: newPeriod
    }, function () {
      this.drawChart();
    });
  },

  handleEmailButtonClick: function (event) {
    event.preventDefault();
    this.setState({mailPopupOpened: !this.state.mailPopupOpened});
  },

  sendEmail: function () {
    apiRequest('POST', '/api/v1/account/reports/'+this.props.reportType+'/send-email', {
      email: this.state.mail
    }, function(err, data) {
      if (err) {
        return alert('Ошибка отправки сообщения. Пожалуйста, попробуйте еще раз через некоторое время.');
      }

      alert('Сообщение успешно отправлено.');
      this.setState({mail: '', mailPopupOpened: false});
    }.bind(this));
  },

  isThisFirstPage: function () {
    return this.getChartData(this.state.currentPage - 1).length === 1;
  },

  isThisLastPage: function () {
    return this.getChartData(this.state.currentPage + 1).length === 1;
  },

  render: function () {
    var disabled = !/^.+@.+\..{2,}$/.test(this.state.mail);

    return (
      <div className="report">
        <div className="report__in">
          <table className="report__table">
            <tr>
              <td>
                <a className="link link_black" href={this.props.backLink}>
                  <span>Список отчетов</span>
                  <i className="icon icon-arr-circle-left"></i>
                  <i className="icon icon-arr-circle-left-fill"></i>
                </a>
              </td>
              <td>Текущее значение</td>
              <td>Динамика</td>
            </tr>
            <tr>
              <td dangerouslySetInnerHTML={{__html: this.props.report.title}} />
              <td><strong>{this.props.report.currentValue}</strong></td>
              <td><i className={'icon ' + this.props.report.statusClass}></i></td>
            </tr>
          </table>
          <div className="report__right">
            <div className="btn-circles">
              <a href="#" onClick={this.handlePrintButtonClick}>
                <i className="icon icon-print-circle"></i>
              </a>
              <a href="#" onClick={this.handleEmailButtonClick}>
                <i className="icon icon-main-circle"></i>
              </a>
              <div className="report__send-in" style={{display: this.state.mailPopupOpened ? 'block' : 'none'}}>
                <div className="field field_rows">
                  <div className="field__label">Отправить результат тестирования по электронной почте</div>
                  <div className="field__in">
                    <input className="input" type="text" placeholder="email получателя" valueLink={this.linkState('mail')} />
                  </div>
                </div>
                <button className="button" disabled={disabled} onClick={this.sendEmail}>Отправить</button>
              </div>
            </div>
          </div>
          {this.props.reportType == 'isr' &&
            <div className="h h_2 h_center">
              <p>{this.props.report.currentValue} - настолько вы соблюдаете рекомендации</p>
              <p>{this.props.report.valueNote}</p>
            </div>
          }
          {this.props.report.isWarningVisible &&
            <button className="button button_wide" onClick={this.handleGoToDoctorLinkClick}>
              <i className="icon icon-info-triangle-big"></i>
              <span>Обратиться к врачу</span>
            </button>
          }
        </div>
        <div className="h-wrap h-wrap_h2 h-wrap_center">
          <div className="h h_2">Отчет за:</div>
          <div className="sorter">
            {this.props.report.periodFormat == Report.PERIOD_FORMAT_WEEK &&
              <a href="#"
                onClick={this.handlePeriodButtonClick.bind(this, Report.PERIOD_MONTH)}
                className={this.state.currentPeriod === Report.PERIOD_MONTH && 'is-active'}
              >месяц</a>
            }
            <a href="#"
              onClick={this.handlePeriodButtonClick.bind(this, Report.PERIOD_QUARTER)}
              className={this.state.currentPeriod === Report.PERIOD_QUARTER && 'is-active'}
            >квартал</a>
            <a href="#"
              onClick={this.handlePeriodButtonClick.bind(this, Report.PERIOD_YEAR)}
              className={this.state.currentPeriod === Report.PERIOD_YEAR && 'is-active'}
            >год</a>
          </div>
        </div>
        <div className="graph">
          <div className="graph__cell">
            {!this.isThisFirstPage() &&
              <button className="graph__prev" onClick={this.handlePrevButtonClick}>
                <i className="icon icon-arr-left"></i>
              </button>
            }
          </div>
          <div id="google-chart" className="graph__cell" style={{width: "720px", height: "500px"}}>
          </div>
          <div className="graph__cell">
            {!this.isThisLastPage() &&
              <button className="graph__next" onClick={this.handleNextButtonClick}>
                <i className="icon icon-arr-right"></i>
              </button>
            }
          </div>
        </div>
      </div>
    );
  }
});

module.exports = Report;