`/** @jsx React.DOM */`

React = require "react"

ModsMixin = require "Engage360d/mixins/ModsMixin"

Button = require "Engage360d/components/button/Button"
DropDown = require "Engage360d/components/dropdown/DropDown"

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
