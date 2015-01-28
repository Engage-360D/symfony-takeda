/**
 * cardiomagnyl
 */

var React = require('react');

var selectStyle = {border: '1px solid black', background: 'white', width: '100%', padding: 5};
var iconStyle = {fontSize: 8, position: 'absolute', marginLeft: -27, marginTop: -14, background: 'white', padding: 6};

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

  getLink: function() {
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

    return {
      value: hash,
      requestChange: this.handleChange
    };
  },

  renderOption: function(option, index) {
    return (
      <option value={option.hash} key={index}>{option.text}</option>
    );
  },

  render: function() {
    return (
      <div>
        <select style={selectStyle}
                valueLink={this.getLink()}>
          {this.props.options.map(this.renderOption)}
        </select>
        <button><i className="icon icon-arr-down" style={iconStyle}></i></button>
      </div>
    );
  }
});

module.exports = Select;
