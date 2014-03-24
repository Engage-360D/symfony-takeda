`/** @jsx React.DOM */`

React = require "React"

Editor = require "./../../../../components/editor/Editor"
Input = require "./../../../../components/form/field/Input"
Field = require "./../../../../components/form/Field"
Label = require "./../../../../components/form/Label"
Panel = require "./../../../../components/panel/Panel"
PanelHeader = require "./../../../../components/panel/PanelHeader"
PanelBody = require "./../../../../components/panel/PanelBody"
PanelFooter = require "./../../../../components/panel/PanelFooter"
Column = require "./../../../../components/column/Column"
Button = require "./../../../../components/button/Button"

PageDetail = React.createClass
  getDefaultProps: ->
    page: null

  getInitialState: ->
    page: @props.page

  onChangeBlocks: (data) ->
    page = @state.page
    page.blocks = data
    @setState page: page

  onChangeTitle: (event) ->
    page = @state.page
    page.title = event.target.value
    @setState page: page

  render: ->
    `(
      <div className="PageDetail">
        <Panel>
          <PanelHeader title="Редактирование страницы"></PanelHeader>
          <PanelBody>
            <Field>
              <Column mods={["Size3"]}>
                <Label>Заголовок</Label>
              </Column>
              <Column mods={["Size6"]}>
                <Input value={this.state.page.title} onChange={this.onChangeTitle}/>
              </Column>
            </Field>
            <Field>
              <Column mods={["Size3"]}>
                <Label>Контент</Label>
              </Column>
              <Editor blocks={this.state.page.blocks} onChange={this.onChangeBlocks}/>
            </Field>
          </PanelBody>
          <PanelFooter>
            <Column mods={["Size3"]}></Column>
            <Column mods={["Size6"]}>
              <Button mods="Primary" onClick={this.props.onSave}>Сохранить</Button>
              <Button onClick={this.props.onCancel}>Отмена</Button>
            </Column>
          </PanelFooter>
        </Panel>
      </div>
    )`

module.exports = PageDetail
