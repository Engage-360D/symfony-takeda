var React = require('react');
var cx = require('react/lib/cx');
var RangeMixin = require('attreactive-mixins/lib/fields/RangeMixin');

var Range = React.createClass({
  mixins: [RangeMixin],

  getDefaultProps: function() {
    return {
      revertBackgroud: false
    };
  },

  render: function() {
    var classes = cx({
      'range': true,
      'is-revert': this.props.revertBackgroud
    });

    return (
      <div className="field__in">
        <div className={classes} ref="wrapper">
          <div className="range__from"><span>{this.props.min}</span><i></i></div>
          <div className="range__to"><span>{this.props.max}</span><i></i></div>
          <div className="range__val">
            <span ref="handle" style={{left: this.getPercentageString()}}>{this.props.valueLink.value}</span>
          </div>
          <div className="range__in">
            <div className="range__line" ref="line"></div>
          </div>
        </div>
      </div>
    );
  }
});

module.exports = Range;
