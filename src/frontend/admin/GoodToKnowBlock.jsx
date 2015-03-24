/**
 * Engage-360D Admin
 * @jsx React.DOM
 */

var React = require("react");
var PageBlockMixin = require('engage-360d-admin/components/pageBlocks/PageBlockMixin');
var FileInput = require('engage-360d-admin/components/form/FileInput');

var GoodToKnowBlock = React.createClass({
  mixins: [PageBlockMixin],

  render: function() {
    var linkState = function(key) {
      return {
        value: this.state[key],
        requestChange: function(value) {
          this.state[key] = value;
          this.props.update(this.state);
        }.bind(this)
      };
    }.bind(this);

    return (
      <div className="panel">
        <div className="panel-heading">
          <div className="panel-btns">
            <a href="#" className="panel-close" onClick={this.props.remove}>×</a>
          </div>
          <h3 className="panel-title">Блок "Полезно знать"</h3>
          <div className="form-horizontal form-bordered">
            <div className="form-group">
              <div className="row">
                <label className="col-sm-3 control-label">Цвет</label>
                <div className="col-sm-6">
                  <input type="text" className="form-control mb15" valueLink={linkState('color')} />
                </div>
              </div>
              <div className="row">
                <label className="col-sm-3 control-label">Изображение</label>
                <div className="col-sm-6">
                  <FileInput valueLink={linkState('image')} className="mb15" adminSetup={this.props.adminSetup} />
                </div>
              </div>
              <div className="row">
                <label className="col-sm-3 control-label">Заголовок</label>
                <div className="col-sm-6">
                  <input type="text" className="form-control mb15" valueLink={linkState('title')} />
                </div>
              </div>
              <div className="row">
                <label className="col-sm-3 control-label">Текст</label>
                <div className="col-sm-6">
                  <textarea className="form-control mb15" valueLink={linkState('content')} />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }
});

module.exports = GoodToKnowBlock;
