var React = require('react');
var cx = require('react/lib/cx');
var RangeMixin = require('attreactive-mixins/lib/fields/RangeMixin');
var $ = require('jquery');

var Range = React.createClass({
  mixins: [RangeMixin],

  getDefaultProps: function() {
    return {
      revertBackgroud: false,
      nullValueLabel: ''
    };
  },

  componentDidMount: function() {
    if (!this.refs || !this.refs.handle) {
      return;
    }

    var handleNode = this.refs.handle.getDOMNode();
    $(handleNode).mousedown(this.__handleMouseDown);
  },

  __handleMouseDown: function(event) {
    event.preventDefault();

    if (!this.refs || !this.refs.wrapper) return;

    var wrapperNode = this.refs.wrapper.getDOMNode();
    var leftPosition = $(wrapperNode).offset().left;
    var rightPosition = leftPosition + $(wrapperNode).width();
    var width = rightPosition - leftPosition;
    var stepSize = width / (this.props.max - this.props.min);

    this.setState({
      catched: true,
      leftPosition: leftPosition,
      rightPosition: rightPosition,
      width: width,
      stepSize: stepSize
    }, function() {
      $(document).mousemove(this.__handleMouseMove);
      $(document).mouseup(this.__handleMouseUp);
    }.bind(this));
  },

  __handleMouseMove: function(event) {
    if (!this.props.valueLink) return;

    var value;
    var position = event.clientX - this.state.leftPosition;
    if (position > this.state.width) {
      position = this.state.width;
    }
    if (position < 0) {
      position = 0;
      if (this.props.nullValueLabel !== '') {
        value = null;
      }
    }

    if (value !== null) {
      value = Math.round((position / this.state.stepSize + this.props.min) / this.props.step) * this.props.step;
    }

    if (value != this.getValue()) {
      this.props.valueLink.requestChange(value);
    }
  },

  __handleMouseUp: function() {
    this.setState({
      catched: false
    });

    $(document).off('mousemove', this.__handleMouseMove);
    $(document).off('mouseup', this.__handleMouseUp);

    if (this.props.onMouseUp) {
      this.props.onMouseUp();
    }
  },

  _getPercentage: function() {
    var size = this.props.max - this.props.min;

    return this.getValue() !== null ?
      (this.getValue() - this.props.min) / size * 100 : -26;
  },

  _getPercentageString: function() {
    return this._getPercentage() + '%';
  },

  render: function() {
    var classes = cx({
      'range': true,
      'is-revert': this.props.revertBackgroud,
      'has-null-value': this.props.nullValueLabel !== ''
    });

    return (
      <div className="field__in">
        <div className={classes} ref="wrapper">
          {this.props.nullValueLabel !== '' &&
            <div className="range__null"><span>{this.props.nullValueLabel}</span><i></i></div>
          }
          <div className="range__from"><span>{this.props.min}</span><i></i></div>
          <div className="range__to"><span>{this.props.max}</span><i></i></div>
          <span className="range__val" ref="handle" style={{left: this._getPercentageString()}}>{this.props.valueLink.value}</span>
          <div className="range__in">
            <div className="range__line" ref="line"></div>
          </div>
        </div>
      </div>
    );
  }
});

module.exports = Range;
