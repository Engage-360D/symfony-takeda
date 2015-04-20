var React = require('react');

var CANVAS_WIDTH = 750;
var CANVAS_HEIGHT = 260;
var TOP_PADDING = 50;
var ROW_HEIGHT = 60;
var MAIN_COLOR = '#AA88F1';
var ALT_COLOR = 'white';
var LEFT_COLUMN_WIDTH = 110;
var GRAPH_WIDTH = CANVAS_WIDTH - LEFT_COLUMN_WIDTH - 120;
var MAX_VALUE = 50;
var LINE_HEIGHT = 40;

var Graph = React.createClass({
  componentDidMount: function() {
    this.paper = Raphael(this.refs.canvas.getDOMNode(), CANVAS_WIDTH, CANVAS_HEIGHT);
    this.draw();
  },

  componentDidUpdate: function() {
    this.paper.clear();
    this.draw();
  },

  text: function(x, y, text) {
    return this.paper.text(x, y, text)
               .attr({
                 'font': '"HelveticaNeue", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif'
               });
  },

  line: function(x1, y1, x2, y2) {
    return this.paper.path('M'+x1+','+y1+'L'+x2+','+y2)
               .translate(0.5, 0.5);
  },

  draw: function() {
    var rows = this.props.data;

    this.paper.rect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT)
        .attr({
          'stroke': 'none',
          'fill': MAIN_COLOR
        });

    this.text(
      LEFT_COLUMN_WIDTH,
      TOP_PADDING + ROW_HEIGHT * rows.length + 10,
      0
    ).attr({
      'fill': ALT_COLOR,
      'font-size': 14,
      'opacity': 0.5
    });

    this.line(
      LEFT_COLUMN_WIDTH,
      TOP_PADDING,
      LEFT_COLUMN_WIDTH,
      TOP_PADDING + ROW_HEIGHT * rows.length
    ).attr({
      'stroke': ALT_COLOR
    });

    for (var lineIndex = 10; lineIndex <= MAX_VALUE; lineIndex += 10) {
      this.line(
        LEFT_COLUMN_WIDTH + lineIndex / MAX_VALUE * GRAPH_WIDTH,
        TOP_PADDING,
        LEFT_COLUMN_WIDTH + lineIndex / MAX_VALUE * GRAPH_WIDTH,
        TOP_PADDING + ROW_HEIGHT * rows.length
      ).attr({
        'stroke': ALT_COLOR,
        'stroke-dasharray': '-',
        'opacity': 0.5
      });

      this.text(
        LEFT_COLUMN_WIDTH + lineIndex / MAX_VALUE * GRAPH_WIDTH,
        TOP_PADDING + ROW_HEIGHT * rows.length + 10,
        lineIndex
      ).attr({
        'fill': ALT_COLOR,
        'font-size': 14,
        'opacity': 0.5
      });
    }

    for (var rowIndex = 0; rowIndex < rows.length; rowIndex++) {
      var row = rows[rowIndex];
      var cols = row.data;

      var rowX = 0;
      var rowY = rowIndex * ROW_HEIGHT + TOP_PADDING;

      this.text(rowX, rowY + ROW_HEIGHT / 2, row.title)
          .attr({
            'font-size': 15,
            'fill': ALT_COLOR,
            'text-anchor': 'start'
          });

      for (var colIndex = 0; colIndex < cols.length; colIndex++) {
        var col = cols[colIndex];
        var width = col.value / MAX_VALUE * GRAPH_WIDTH;
        var rectFill, textX, textFill;

        switch (col.style) {
          case 'solid':
            rectFill = ALT_COLOR;
            break;
          case 'dashed':
            rectFill = 'url(/graph_dashed_bg.png)';
            break;
          default:
            rectFill = 'red';
        }

        switch (col.valuePosition) {
          case 'outside_right':
            textX = rowX + LEFT_COLUMN_WIDTH + width + 20;
            textFill = ALT_COLOR;
            break;
          case 'inside':
          default:
            textX = rowX + LEFT_COLUMN_WIDTH + width / 2;
            textFill = MAIN_COLOR;
        }

        this.paper.rect(rowX + LEFT_COLUMN_WIDTH + 1, rowY + (ROW_HEIGHT - LINE_HEIGHT) / 2, width, LINE_HEIGHT)
            .attr({
              'stroke': ALT_COLOR,
              'fill': rectFill
            });

        this.text(textX, rowY + ROW_HEIGHT / 2, col.value)
            .attr({
              'fill': textFill,
              'font-size': 22,
            });

        if (rowIndex === 0) {
          var colTitleLeft;

          switch (col.titlePosition) {
            case 'center':
              colTitleLeft = LEFT_COLUMN_WIDTH + width + 20 - width / 2;
              break;
            case 'right':
            default:
              colTitleLeft = LEFT_COLUMN_WIDTH + width;
          }

          this.line(
            colTitleLeft - 20,
            rowY + 9,
            colTitleLeft,
            rowY - 11
          ).attr({
            'stroke': ALT_COLOR
          });

          this.text(colTitleLeft + 4, rowY - 20, col.title)
              .attr({
                'fill': ALT_COLOR,
                'font-size': 13,
                'text-anchor': 'start'
              });
        }
      }
    }
  },

  render: function() {
    return (
      <div class="main__graph">
        <div ref="canvas" />
      </div>
    );
  }
});

module.exports = Graph;
