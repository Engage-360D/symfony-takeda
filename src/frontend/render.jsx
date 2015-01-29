/**
 * cardiomagnyl
 */

var $ = require('jquery');
var React = require('react');

function render(Component, id, props) {
  $(function() {
    React.render(<Component {...props} />, document.getElementById(id));
  });
}

module.exports = render;
