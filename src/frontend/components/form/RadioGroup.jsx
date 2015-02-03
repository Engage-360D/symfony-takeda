/**
 * cardiomagnyl
 */

var React = require('react');

var RadioGroup = React.createClass({
  render: function() {
    return (
      <span>
        {this.props.options.map(function(option) {
          return (
            <label className="field__radio">
              <input type="radio" checked={this.props.valueLink.value == option.value}
                     onChange={this.props.valueLink.requestChange.bind(null, option.value)} />
              <span>{option.text}</span>
            </label>
          );
        }.bind(this))}
      </span>
    );
  }
});

module.exports = RadioGroup;
