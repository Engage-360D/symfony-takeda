/**
 * cardiomagnyl
 */

/*jslint node: true*/

'use strict';

var React = require('react');

var NavigationButton = React.createClass({

  statics: {
    TYPE_NEXT: 'NEXT',
    TYPE_PREV: 'PREV'
  },

  handleClick: function () {
    this.props.onClick();
  },

  isTypeNext: function () {
    return this.props.type === NavigationButton.TYPE_NEXT;
  },

  render: function () {
    return (
      <button className={this.isTypeNext() ? 'post__next' : 'post__prev'} onClick={this.handleClick}>
        <i className={'icon ' + (this.isTypeNext() ? 'icon-arr-right' : 'icon-arr-left')}></i>
      </button>
    );
  }
});

module.exports = NavigationButton;