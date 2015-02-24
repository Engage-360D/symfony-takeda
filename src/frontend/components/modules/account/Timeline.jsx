/**
 * cardiomagnyl
 */

/*jslint node: true*/

"use strict";

// TODO refactor the whole component!!!

var React = require('react/addons');
var cx = React.addons.classSet;
var moment = require('moment');
var RadioGroup = require('../../form/RadioGroup');
var apiRequest = require('../../../utilities/apiRequest');
var utils = require('../../../utilities/misc');
var capitaliseFirstLetter = utils.capitaliseFirstLetter;
var inflectMinutes = utils.getNounInflector(["минута", "минуты", "минут"]);
var inflectPills = utils.getNounInflector(["таблетка", "таблетки", "таблеток"]);

moment.locale('ru');

var CellExercise = React.createClass({
  render: function () {
    var task = this.props.task;

    var cellClass = cx({
      'time': true,
      'time_no-buttons': task.isCompleted !== null,
      'is-error': task.isCompleted === false
    });

    return (
      <div className="timeline__cell">
        <div className={cellClass}>
          <div className="time__in">
            <button className="time__up"
              onClick={this.props.valueLink.requestChange.bind(null, this.props.valueLink.value + 10)}>
              <i className="icon icon-arr-top"></i>
            </button>
            <div className="time__value">{this.props.valueLink.value}</div>
            <button className="time__down"
              onClick={this.props.valueLink.requestChange.bind(null, Math.max(0, this.props.valueLink.value - 10))}>
              <i className="icon icon-arr-down"></i>
            </button>
          </div>
          <div className="time__size">{inflectMinutes(this.props.task.exerciseMins)}</div>
        </div>
      </div>
    );
  }
});

var CellDiet = React.createClass({
  render: function () {
    var options = [
      {
        text: "Я следовал рекомендациям",
        value: true
      },
      {
        text: "Не удалось",
        value: false
      }
    ];

    return (
      <div className="timeline__cell">
        <div className="field field_radio-list">
          {this.props.task.isCompleted === null ?
            <RadioGroup options={options} valueLink={this.props.valueLink} /> :
            <p>{this.props.task.isCompleted ? options[0].text : options[1].text}</p>
          }
        </div>
      </div>
    );
  }
});

var CellPill = React.createClass({
  render: function () {
    var pill = this.props.pills.filter(function (p) {
      return this.props.task.links.pill == p.id;
    }.bind(this))[0] || {};

    var prescription = [
      pill.name,
      pill.time && pill.time.slice(0, 5),
      pill.quantity,
      inflectPills(pill.quantity)
    ].join(' ');

    return (
      <div className="timeline__cell">
        <div className="field field_checkbox-list">

           {this.props.task.isCompleted === null ? (
             <label className="field__checkbox">
               <input type="checkbox"
                 checked={this.props.valueLink.value}
                 onChange={this.props.valueLink.requestChange}
               />
               <span>{prescription}<i className="icon icon-ok"></i></span>
             </label>
           ) : (
             <p>{prescription}</p>
           )}
        </div>
      </div>
    );
  }
});

var StatusButton = React.createClass({
  render: function () {
    var button;
    var statusClass;
    var statusIconClass;

    if (this.props.task.isCompleted === null) {
      button = (
        <a className="timeline__btn" href="#" onClick={this.props.onClick}>
          <i className="icon icon-ok-circle-big-thin"></i>
        </a>
      );
    } else {
      statusClass = cx({
        'timeline__status': true,
        'is-error': this.props.task.isCompleted === false,
        'is-done': this.props.task.isCompleted === true
      });

      statusIconClass = cx({
        'icon': true,
        'icon-close-circle': this.props.task.isCompleted === false,
        'icon-ok-circle-big-thin': this.props.task.isCompleted === true
      });

      button = (
        <span className={statusClass}>
          <i className={statusIconClass}></i>
        </span>
      );
    }

    return button;
  }
});

var Task = React.createClass({
  statics: {

    TYPE_EXERCISE: 'exercise',
    TYPE_DIET: 'diet',
    TYPE_PILL: 'pill',

    getTitle: function (task) {
      var name = '';

      switch (task.type) {
        case Task.TYPE_EXERCISE:
          name = 'Физическая нагрузка';
          break;
        case Task.TYPE_DIET:
          name = 'Диета';
          break;
        case Task.TYPE_PILL:
          name = 'Прием таблеток';
          break;
      }

      return name;
    },

    getLink: function (task) {
      var link = {};

      switch (task.type) {
        case Task.TYPE_EXERCISE:
          link.text = 'Мои рекомендации по физической нагрузке';
          link.href = '#';
          break;
        case Task.TYPE_DIET:
          link.text = 'Мои рекомендации по диете';
          link.href = '#';
          break;
        case Task.TYPE_PILL:
          link.text = '';
          link.href = '#';
          break;
      }

      return link;
    },

    // TODO rename?
    isCompleted: function (task) {
      return task.isCompleted !== null;
    },

    isIncomplete: function (task) {
      return task.isCompleted === null;
    }
  },

  getInitialState: function () {
    return {
      exerciseMins: this.props.task.exerciseMins || 0,
      isCompleted: this.props.task.type === Task.TYPE_DIET ? null : false
    };
  },

  submitTask: function (e) {
    e.preventDefault();

    var data = {};

    if (this.props.task.type === Task.TYPE_EXERCISE) {
      data.exerciseMins = this.state.exerciseMins;
    } else {
      if (this.state.isCompleted === null) {
        return;
      }
      data.isCompleted = this.state.isCompleted;
    }

    apiRequest(
      'POST',
      '/api/v1/account/timeline/tasks/' + this.props.task.id + '?_method=PUT',
      data,
      function(err, data) {
        if (err) {
          alert("Ошибка. Попробуйте позже.");
          return;
        }

        this.props.cb(data.data);
      }.bind(this)
    );
  },

  render: function () {
    var cell;

    switch (this.props.task.type) {
      case Task.TYPE_EXERCISE:
        cell = (
          <CellExercise {...this.props}
            valueLink={{
              value: this.state.exerciseMins,
              requestChange: function (value) {
                this.setState({exerciseMins: value});
              }.bind(this)
            }}
          />
        );
        break;
      case Task.TYPE_DIET:
        cell = (
          <CellDiet {...this.props}
            valueLink={{
              value: this.state.isCompleted,
              requestChange: function (value) {
                this.setState({isCompleted: value});
              }.bind(this)
            }}
          />
        );
        break;
      case Task.TYPE_PILL:
        cell = (
          <CellPill {...this.props}
            valueLink={{
              value: this.state.isCompleted,
              requestChange: function (value) {
                this.setState({isCompleted: !this.state.isCompleted});
              }.bind(this)
            }}
          />
        );
        break;
      default:
        cell = <div />;
        break;
    }

    return (
      <div className="timeline__row">
        <div className="timeline__cell">
          <div className="timeline__title">{Task.getTitle(this.props.task)}</div>
          <div className="timeline__recomm">
            <a href={Task.getLink(this.props.task).href}>{Task.getLink(this.props.task).text}</a>
          </div>
        </div>
        {cell}
        <StatusButton task={this.props.task} onClick={this.submitTask.bind(this)} />
      </div>
    );
  }
});

var Day = React.createClass({
  getTitle: function () {
    var title;
    var task = this.props.tasks[0];
    var dateStr = [
      task.id.slice(0, 4),
      task.id.slice(4, 6),
      task.id.slice(6, 8)
    ].join('-');

    title = moment().isSame(moment(dateStr), 'day') ? 'Сегодня ' : '';
    title += moment(dateStr).format("dddd, DD MMMM YYYY");
    title = capitaliseFirstLetter(title);

    return title;
  },

  render: function () {
    if (this.props.tasks.length === 0) {
      return null;
    }

    return (
      <div className="timeline">
        <div className="timeline__head">{this.getTitle()}</div>
        {this.props.tasks.map(function (task) {
          return (<Task key={task.id} task={task} pills={this.props.pills} cb={this.props.cb} />);
        }.bind(this))}
      </div>
    );
  }
});

var Timeline = React.createClass({
  statics: {
    TAB_TODAY: 1,
    TAB_COMPLETED: 2
  },

  getInitialState: function () {
    return {
      activeTab: Timeline.TAB_TODAY,
      // TODO refactor this hack
      completedTasks: []
    };
  },

  openTab: function (tabName, e) {
    e.preventDefault();
    this.setState({activeTab: tabName});
  },

  renderTabs: function () {
    return (
      <div className="h-wrap">
        <div className="h">Задачи:</div>
        <div className="sorter">
          <a
            className={this.state.activeTab === Timeline.TAB_TODAY ? 'is-active' : ''}
            onClick={this.openTab.bind(this, Timeline.TAB_TODAY)}
            href="#">на сегодня</a>
          <a
            className={this.state.activeTab === Timeline.TAB_COMPLETED ? 'is-active' : ''}
            onClick={this.openTab.bind(this, Timeline.TAB_COMPLETED)}
            href="#">выполненные</a>
        </div>
      </div>
    );
  },

  markTaskAsCompleted: function (task) {
    this.setState({completedTasks: this.state.completedTasks.concat(task)});
  },

  isInArrayOfCompleted: function (task) {
    return this.state.completedTasks.filter(function (completedTask) {
      return task.id == completedTask.id;
    }).length === 1;
  },

  render: function () {
    var tasks = this.props.timeline.linked.tasks.slice();
    var days = this.props.timeline.data.slice();
    var tasksByDay = [];
    var i;

    // Filter out tasks which shouldn't be displayed
    tasks = tasks.filter(
      this.state.activeTab === Timeline.TAB_TODAY ?
        function (task) {
          return task.isCompleted === null && !this.isInArrayOfCompleted(task);
        }.bind(this) :
        function (task) {
          return task.isCompleted !== null || this.isInArrayOfCompleted(task);
        }.bind(this)
    );

    // Ugh!
    for (i = 0; i < tasks.length; i++) {
      var task = tasks[i];

      if (this.isInArrayOfCompleted(tasks[i])) {

        tasks[i] = this.state.completedTasks.filter(function (completedTask) {
          return task.id == completedTask.id;
        })[0];
      }
    }

    // Sort days in DESC order
    days.sort(function (a, b) {
      return moment(b.date).diff(moment(a.date), 'day');
    });

    // Group tasks by day
    tasksByDay = days.map(function (day) {
      var ids = day.links.tasks;
      return tasks.filter(function (task) {
        return ids.indexOf(task.id) !== -1;
      });
    });

    return (
      <div>
        {this.renderTabs()}
        {tasksByDay.map(function (tasks) {
          return <Day tasks={tasks} pills={this.props.pills} cb={this.markTaskAsCompleted.bind(this)} />;
        }.bind(this))}
      </div>
    );
  }
});

module.exports = Timeline;