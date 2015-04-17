/**
 * @jsx React.DOM
 */

var React = require("react");
var CKEditorField = require('engage-360d-admin/components/fields/CKEditor');

var CKEditor = React.createClass({
  render: function() {
    return this.transferPropsTo(
      <CKEditorField fileBrowser="/filebrowser/plugin.js"
                     fileBrowserBrowseUrl="/fileman/index.html"
                     fileBrowserImageBrowseUrl="/fileman/index.html?type=image" />
    );
  }
});

module.exports = CKEditor;
