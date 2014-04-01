`/** @jsx React.DOM */`

React = require "React"

Modal = React.createClass
  getDefaultProps: ->
    show: false
    title: null

  onClose: ->
    @props.onClose() if @props.onClose

  render: ->
    return `(<div/>)` unless @props.show

    `(
      <div>
        <div className="ModalBackdrop" onClick={this.onClose}>
        </div>
        <div className="Modal">
          <div className="ModalHeader">
            <button className="ModalClose" onClick={this.onClose}>×</button>
            <span>{this.props.title}</span>
          </div>
          <div className="ModalBody">
            {this.props.children}
          </div>
        </div>
      </div>
    )`

module.exports = Modal
