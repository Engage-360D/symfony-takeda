`/** @jsx React.DOM */`

React = require "React"

Grid = React.createClass
  getDefaultProps: ->
    columns: []
    items: []
    current: null

  createRowSelectHandler: (row) ->
    =>
      @setState current: row
      @props.onSelect row if @props.onSelect
  
  renderHeaderCell: (cell) ->
    `(
      <th className="GridHeaderCell">{cell.title}</th>
    )`

  renderHeader: ->
    `(
      <thead className="GridHeader">
        <tr>
          {this.props.columns.map(this.renderHeaderCell)}
        </tr>
      </thead>
    )`

  renderBodyRowCell: (row) ->
    (column) =>
      `(
        <td className="GridBodyCell">{row[column.name]}</td>
      )`

  renderBodyRow: (row) ->
    `(
      <tr className="GridBodyRow" onClick={this.createRowSelectHandler(row)}>
        {this.props.columns.map(this.renderBodyRowCell(row))}
      </tr>
    )`

  renderBody: ->
    `(
      <tbody className="GridBody">
        {this.props.items.map(this.renderBodyRow)}
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
