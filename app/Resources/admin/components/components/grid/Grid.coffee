`/** @jsx React.DOM */`

React = require "React"

Icon = require "Engage360d/components/icon/Icon"
Input = require "Engage360d/components/form/field/Input"

Grid = React.createClass
  getDefaultProps: ->
    columns: []
    actions: []
    items: []
    current: null

  getInitialState: ->
    filters: {}

  createActionChangeHandler: (action, row) ->
    => @props.onHandleAction action, row if @props.onHandleAction

  createFilterChangeHandler: (column) ->
    (event) =>
      filters = @state.filters
      filters[column.name] = event.target.value
      @setState filters: filters

  filter: ->
    filters = @state.filters
    @props.items.filter (row) ->
      match = true
      for field, value of filters
        if value and value.length > 0
          match = false if row[field].toString().indexOf(value.toString()) is -1
      match

  renderHeaderCell: (column) ->
    `(
      <th className="GridHeaderCell" width={column.width}>{column.title}</th>
    )`

  renderHeaderCellFilter: (column) ->
    handler = @createFilterChangeHandler column
    `(
      <th className="GridHeaderCell GridHeaderCellFilter" width={column.width}>
        <Input onChange={handler}/>
      </th>
    )`

  renderHeader: ->
    actions = null
    actions = `(<th className="GridHeaderCell"></th>)` if @props.actions.length > 0

    filters = null
    filters = `(
      <tr>
        {this.props.columns.map(this.renderHeaderCellFilter)}
        <th className="GridHeaderCell GridHeaderCellFilter"></th>
      </tr>
    )`

    `(
      <thead className="GridHeader">
        {filters}
        <tr>
          {this.props.columns.map(this.renderHeaderCell)}
          {actions}
        </tr>
      </thead>
    )`

  renderBodyRowCell: (row) ->
    (column) =>
      `(
        <td className="GridBodyCell">{row[column.name]}</td>
      )`

  renderRowActions: (row) ->
    return if @props.actions.length is 0
    actions = @props.actions.map (action) =>
      handler = @createActionChangeHandler action.name, row
      `(
        <Icon name={action.icon} onClick={handler}/>
      )`

    `(
      <td className="GridBodyCell GridBodyCellActions">{actions}</td>
    )`

  renderBodyRow: (row) ->
    `(
      <tr className="GridBodyRow">
        {this.props.columns.map(this.renderBodyRowCell(row))}
        {this.renderRowActions(row)}
      </tr>
    )`

  renderBody: ->
    `(
      <tbody className="GridBody">
        {this.filter().map(this.renderBodyRow)}
      </tbody>
    )`

  render: ->
    `(
      <table className="Grid">
        {this.renderHeader()}
        {this.renderBody()}
      </table>
    )`

module.exports = Grid
