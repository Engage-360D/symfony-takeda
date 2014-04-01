`/** @jsx React.DOM */`

React = require "React"

ModsMixin = require "Engage360d/mixins/ModsMixin"
Icon = require "Engage360d/components/icon/Icon"

NavigationItem = React.createClass
  mixins: [ModsMixin]

  getDefaultProps: ->
    title: null
    icon: null

  render: ->
    @transferPropsTo(
      `(
        <a href="#" className={this.getClassName("NavigationItem")}>
          <Icon name={this.props.icon}/>
          {this.props.title}
        </a>
      )`
    )

module.exports = NavigationItem
