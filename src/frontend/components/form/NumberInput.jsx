var React = require('react');

var NumberInput = React.createClass({
  getLink: function() {
    return {
      value: this.props.valueLink.value,
      requestChange: function(value) {
        value = Number(value.replace(/[^0-9]/g, ''));
        if (value !== this.props.valueLink.value) {
          this.props.valueLink.requestChange(value);
        }
      }.bind(this)
    }
  },

  render: function() {
    return (
      <input className="input input_w50" type="text" valueLink={this.getLink()} />
    );
  }
});

module.exports = NumberInput;
