/**
 * cardiomagnyl
 */

var React = require('react');
var ReactSelect = require('react-select');

require('react-select/dist/default.css');
require('./Select.css');

var Select = React.createClass({
  getDefaultProps: function() {
    return {
      options: [],
      valueLink: null
    };
  },

  handleChange: function(hash) {
    var selectedOption = this.props.options.filter(function(option) {
      return option.hash === hash;
    }).shift();

    this.props.valueLink.requestChange(
      selectedOption ? selectedOption.value : null
    );
  },

  getValue: function() {
    if (!this.props.valueLink) {
      return null;
    }

    var hash = '';

    if (this.props.valueLink) {
      var value = this.props.valueLink.value;
      var option = this.props.options.filter(function(option) {
        return option.value === value;
      }).shift();

      if (option) {
        hash = option.hash;
      }
    }

    return hash;
  },

  render: function() {
    var options = this.props.options.map(function(option) {
      return {value: option.hash, label: option.text};
    });

    return (
      <ReactSelect options={options}
                   value={this.getValue()}
                   onChange={this.handleChange}
                   clearable={false}
                   matchProp="label" />
    );
  }
});

module.exports = Select;
