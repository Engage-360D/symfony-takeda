var React = require('react');
var async = require('async');
var Select = require('../../form/Select');
var apiRequest = require('../../../utilities/apiRequest');
var LinkedStateMixin = require('react/lib/LinkedStateMixin');

function itemAddress(item) {
  return [
    item.parsedTown,
    item.parsedStreet,
    item.parsedHouse,
    item.parsedCorpus,
    item.parsedBuilding
  ].filter(function(i) { return !!i; }).join(', ');
}

var Institutions = React.createClass({
  mixins: [LinkedStateMixin],

  getInitialState: function() {
    return {
      items: this.props.results,
      parsedTown: this.props.parsedTown,
      specialization: ""
    };
  },

  componentDidMount: function() {
    this.initializeMap();
  },

  componentDidUpdate: function(nextProps, nextState) {
    var parsedTowns = this.props.parsedTowns.map(function(p) { return p.toLowerCase(); });

    if ((this.state.parsedTown !== nextState.parsedTown || this.state.specialization !== nextState.specialization) && parsedTowns.indexOf(this.state.parsedTown.toLowerCase()) >= 0) {
      this.reloadItems();
    }
  },

  initializeMap: function() {
    ymaps.load(function() {
      this.map = new ymaps.Map(this.refs.map.getDOMNode(), {
        center: [55.76, 37.64],
        zoom: 7
      });

      this.applyItems();
    }.bind(this));
  },

  changeMap: function() {
    if (!this.map) return;
    this.applyItems();
  },

  applyItems: function() {
    if (!this.map) return;

    this.map.geoObjects.removeAll();

    var target;
    if (this.state.items.length > 30) {
      target = new ymaps.Clusterer();
    } else {
      target = this.map.geoObjects;
    }

    this.state.items.forEach(function(item) {
      target.add(new ymaps.Placemark([item.lat, item.lng], {
        clusterCaption: item.name,
        balloonContentHeader: '<a href="/institutions/'+item.id+'" target="blank">'+item.name+'</a>',
        balloonContentBody: '<p>'+itemAddress(item)+'</p><p>'+item.specialization+'</p>'
      }, {
        iconLayout: 'default#image',
        iconImageHref: '/img/icons/mapicon.png',
        iconImageSize: [31, 40],
        iconImageOffset: [-15, -40]
      }));
    }.bind(this));

    if (target !== this.map.getObjects) {
      this.map.geoObjects.add(target);
    }

    var bounds = target.getBounds();
    if (bounds) {
      this.map.setBounds(bounds);
      var zoom = this.map.getZoom();
      if (zoom > 10) {
        zoom = 10;
      }
      this.map.setZoom(zoom);
    }
  },

  reloadItems: function() {
    this.setState({items: []});

    apiRequest('GET', '/api/v1/institutions', {
      parsedTown: this.state.parsedTown,
      specialization: this.state.specialization
    }, function(err, data) {
      if (err) {
        return alert('Unknown error');
      }

      this.setState({items: data.data}, this.changeMap);
    }.bind(this));
  },

  getParsedTownOptions: function() {
    return this.props.parsedTowns.map(function(parsedTown) {
      return {
        value: parsedTown,
        hash: parsedTown,
        text: parsedTown
      };
    });
  },

  renderItem: function(item, index) {
    var address = itemAddress(item);

    return (
      <div className="searcher__item" key={index}>
        <a className="searcher__link" href={"/institutions/" + item.id}>
          <div className="searcher__cell">
            <p><span>{item.name}</span></p>
            <p>{item.specialization}</p>
          </div>
          <div className="searcher__cell">
            <p>{address}</p>
            <p><i>{item.address}</i></p>
          </div>
          <i className="icon icon-arr-right"></i>
        </a>
      </div>
    );
  },

  renderItems: function() {
    return (
      <div className="searcher__list">
        {this.state.items.map(this.renderItem)}
      </div>
    );
  },

  renderSpecialization: function(specialization, index) {
    var change = function(event) {
      event.preventDefault();
      this.setState({specialization: specialization});
    }.bind(this);

    return (
      <a className={this.state.specialization === specialization && 'is-active'}
         onClick={change}
         key={index}
         href="#">
        {specialization || 'все'}
      </a>
    );
  },

  renderSpecializations: function() {
    return this.props.specializations.map(this.renderSpecialization);
  },

  render: function() {
    return (
      <div className="container">
        <div className="head">
          <div className="head__right">
            <div className="head__city">
              <a href="/institutions"><i className="icon icon-marker"></i><span>{this.props.geoIPCity}</span></a>
            </div>
          </div>
          <div className="head__left">
            <div className="h">Компания Такеда</div>
          </div>
        </div>
        <div className="map">
          <div ref="map" style={{width: '100%', height: 500}} />
        </div>
        <div className="searcher">
          <div className="field field_no-wide">
            <div className="field__label">Список учреждений в регионе</div>
            <div className="field__in" style={{width: 200}}>
              <Select options={this.getParsedTownOptions()}
                      valueLink={this.linkState('parsedTown')} />
            </div>
          </div>
          <div className="sorter">
            {this.renderSpecialization('')}
            {this.renderSpecializations()}
          </div>
          {this.renderItems()}
        </div>
      </div>
    );
  }
});

module.exports = Institutions;
