/**
 * cardiomagnyl
 */

/*jslint node: true*/

"use strict";

var React = require('react');
var NavigationButton = require('./NavigationButton');

var NextPageButton = React.createClass({
  handleClick: function () {
    this.props.onClick();
  },

  render: function () {
    return (
      <NavigationButton
        type={NavigationButton.TYPE_NEXT}
        onClick={this.handleClick}
      />
    );
  }
});

module.exports = NextPageButton;