`/** @jsx React.DOM */`

React = require "react"

Modal = React.createClass
  getDefaultProps: ->
    title: null

  getInitialState: ->
    show: true

  onClose: ->
    @props.onClose() if @props.onClose

  render: ->
    return `(<div/>)` unless @state.show

    `(
      <div>
        <div className="ModalBackdrop" onClick={this.onClose}>
        </div>
        <div className="Modal">
          <button className="ModalClose" onClick={this.onClose}></button>
          <div className="ModalTitle">{this.props.title}</div>
          <div className="ModalBody">
            {this.props.children}
          </div>
        </div>
      </div>
    )`

module.exports = Modal
