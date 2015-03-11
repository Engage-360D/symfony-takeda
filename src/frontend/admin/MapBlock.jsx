/**
 * Engage-360D Admin
 * @jsx React.DOM
 */

var React = require("react");
var PageBlockMixin = require('engage-360d-admin/components/pageBlocks/PageBlockMixin');

var MapBlock = React.createClass({
  mixins: [PageBlockMixin],

  render: function() {
    return (
      <div className="panel">
        <div className="panel-heading">
          <div className="panel-btns">
            <a href="#" className="panel-close" onClick={this.props.remove}>×</a>
          </div>
          <h3 className="panel-title">Блок "Карта"</h3>
        </div>
      </div>
    );
  }
});

module.exports = MapBlock;
