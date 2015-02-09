/**
 * cardiomagnyl
 */

var React = require('react');
var cx = require('react/lib/cx');
var moment = require('moment');
var $ = require('jquery');

var attached = [];

$(function() {
  $(document).click(function(event) {
    var $target = $(event.target);

    attached.forEach(function(component) {
      var $node = $(component.getDOMNode());
      if ($node.is($target) || $node.has($target).length > 0) {
        return;
      }
      component.close();
    });
  });
});

var DateInput = React.createClass({
  getDefaultProps: function() {
    return {
      min: moment().subtract(100, 'years'),
      max: moment().add(100, 'years')
    };
  },

  getInitialState: function() {
    return {
      opened: false,
      value: null
    };
  },

  componentWillMount: function() {
    attached.push(this);
  },

  componentWillUnmount: function() {
    attached = attached.filter(function(a) {
      return a !== this;
    }.bind(this));
  },

  open: function() {
    this.setState({opened: true, value: this.getValue()});
  },

  close: function() {
    this.setState({opened: false});
  },

  toggleOpen: function() {
    this.state.opened ? this.close() : this.open();
  },

  requestChange: function() {
    this.props.valueLink.requestChange(this.state.value);
    this.close();
  },

  getValue: function() {
    var value = this.props.valueLink.value;

    if (!value) {
      value = moment();
      if (value.isAfter(this.props.max)) {
        value = moment(this.props.max);
      }
      if (value.isBefore(this.props.min)) {
        value = moment(this.props.min);
      }
    }

    return value;
  },

  renderInner: function() {
    var value = this.state.value;

    function monthName(number) {
      return [
        'Январь',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь'
      ][Number(number)-1];
    }

    var months = [
      moment(value).subtract(1, 'month'),
      moment(value),
      moment(value).add(1, 'month')
    ];

    var days = [
      moment(value).subtract(1, 'day'),
      moment(value),
      moment(value).add(1, 'day'),
    ];

    var years = [
      moment(value).subtract(1, 'year'),
      moment(value),
      moment(value).add(1, 'year'),
    ];

    var setter = function(value, event) {
      event.preventDefault();
      this.setState({value: value});
    }.bind(this);

    var addYear = setter.bind(null, years[2]);
    var addMonth = setter.bind(null, months[2]);
    var addDay = setter.bind(null, days[2]);
    var subtractYear = setter.bind(null, years[0]);
    var subtractMonth = setter.bind(null, months[0]);
    var subtractDay = setter.bind(null, days[0]);

    return (
      <div className="dp__in">
        <button className="dp__close" onClick={this.close}>
          <i className="icon icon-close"></i>
        </button>
        <div className="dp__title">Дата рождения</div>
        <div className="dp__date">
          <div className="dp__row">
            <div className="dp__cell">
              <button className="dp__up" onClick={subtractMonth} style={{display: months[0].isBefore(this.props.min) ? 'none' : 'inline'}}>
                <i className="icon icon-arr-top"></i>
              </button>
            </div>
            <div className="dp__cell">
              <button className="dp__up" onClick={subtractDay} style={{display: days[0].isBefore(this.props.min) ? 'none' : 'inline'}}>
                <i className="icon icon-arr-top"></i>
              </button>
            </div>
            <div className="dp__cell">
              <button className="dp__up" onClick={subtractYear} style={{display: years[0].isBefore(this.props.min) ? 'none' : 'inline'}}>
                <i className="icon icon-arr-top"></i>
              </button>
            </div>
          </div>
          <div className="dp__row">
            <div className="dp__cell">{months[0].format('M')}</div>
            <div className="dp__cell">{days[0].format('DD')}</div>
            <div className="dp__cell">{years[0].format('YYYY')}</div>
          </div>
          <div className="dp__row is-active">
            <div className="dp__cell">{months[1].format('M')}</div>
            <div className="dp__cell">{days[1].format('DD')}</div>
            <div className="dp__cell">{years[1].format('YYYY')}</div>
          </div>
          <div className="dp__row">
            <div className="dp__cell">{months[2].format('M')}</div>
            <div className="dp__cell">{days[2].format('DD')}</div>
            <div className="dp__cell">{years[2].format('YYYY')}</div>
          </div>
          <div className="dp__row">
            <div className="dp__cell">
              <button className="dp__down" onClick={addMonth} style={{display: months[2].isAfter(this.props.max) ? 'none' : 'inline'}}>
                <i className="icon icon-arr-down"></i>
              </button>
            </div>
            <div className="dp__cell">
              <button className="dp__down" onClick={addDay} style={{display: days[2].isAfter(this.props.max) ? 'none' : 'inline'}}>
                <i className="icon icon-arr-down"></i>
              </button>
            </div>
            <div className="dp__cell">
              <button className="dp__down" onClick={addYear} style={{display: years[2].isAfter(this.props.max) ? 'none' : 'inline'}}>
                <i className="icon icon-arr-down"></i>
              </button>
            </div>
          </div>
        </div>
        <div className="dp__footer">
          <button className="button" onClick={this.requestChange}>Выбрать</button>
        </div>
      </div>
    );
  },

  render: function() {
    var classes = cx({
      'dp': true,
      'is-open': this.state.opened
    });

    var value = this.props.valueLink.value;

    return (
      <div className={classes}>
        <div className="dp__value" onClick={this.toggleOpen}>{value ? value.format('DD.MM.YYYY') : 'выбрать дату'}</div>
        <div className="dp__button" onClick={this.toggleOpen}>
          <i className="icon icon-calendar"></i>
          <i className="icon icon-calendar-fill"></i>
        </div>
        {this.state.opened && this.renderInner()}
      </div>
    );
  }
});

module.exports = DateInput;
