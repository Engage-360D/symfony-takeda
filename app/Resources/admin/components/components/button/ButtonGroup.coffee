`/** @jsx React.DOM */`

React = require "React"

ModsMixin = require "./../../mixins/ModsMixin"

Button = require "./Button"
DropDown = require "./../dropdown/DropDown"

ButtonGroup = React.createClass
  mixins: [ModsMixin]

  getDefaultProps: ->
    button: null

  getInitialState: ->
    showDropDown: false

  handleChange: ->
    @setState showDropDown: not @state.showDropDown

  renderDropDown: ->
    return unless @props.children

    `(
      <DropDown show={this.state.showDropDown} mods={this.props.mods}>
        {this.props.children}
      </DropDown>
    )`

  render: ->
    @transferPropsTo(
      `(
        <div className={this.getClassName("ButtonGroup")}>
          <Button onClick={this.handleChange} mods={this.props.mods}>{this.props.button}</Button>
          {this.renderDropDown()}
        </div>
      )`
    )

module.exports = ButtonGroup
