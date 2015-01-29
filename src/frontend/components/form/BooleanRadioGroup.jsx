/**
 * cardiomagnyl
 */

var React = require('react');

var BooleanRadioGroup = React.createClass({
  render: function() {
    return (
      <span>
        <label className="field__radio">
          <input type="radio" checked={this.props.valueLink.value}
                 onChange={this.props.valueLink.requestChange.bind(null, true)} />
          <span>Да</span>
        </label>
        <label className="field__radio">
          <input type="radio" checked={!this.props.valueLink.value}
                 onChange={this.props.valueLink.requestChange.bind(null, false)} />
          <span>Нет</span>
        </label>
      </span>
    );
  }
});

module.exports = BooleanRadioGroup;
