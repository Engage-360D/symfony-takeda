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
      parsedTown: this.props.parsedTowns[0],
      specialization: ""
    };
  },

  componentDidMount: function() {
    this.initializeMap();
  },

  componentDidUpdate: function(nextProps, nextState) {
    if (this.state.parsedTown !== nextState.parsedTown || this.state.specialization !== nextState.specialization) {
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

    async.map(this.state.items, function(item, callback) {
      var address = itemAddress(item);
      ymaps.geocode(address, {results: 1, json: true})
           .then(function(res) {
             var coords = res.GeoObjectCollection.featureMember[0].GeoObject.Point.pos.split(' ').map(Number).reverse();
             this.map.geoObjects.add(new ymaps.Placemark(coords, {}, {
               iconLayout: 'default#image',
               iconImageHref: '/img/icons/mapicon.png',
               iconImageSize: [31, 40],
               iconShape: {
                 type: 'Rectangle',
                 coordinates: [[0, 0], [31, 40]]
               }
             }));
             callback(null);
           }.bind(this), function(err) {
             callback(err);
           });
    }.bind(this), function(err) {
      var bounds = this.map.geoObjects.getBounds();
      if (bounds) {
        this.map.setBounds(bounds);
        var zoom = this.map.getZoom() - 1;
        if (zoom > 10) {
          zoom = 10;
        }
        this.map.setZoom(zoom)
      }
    }.bind(this));
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
              <a href="#"><i className="icon icon-marker"></i><span>Москва</span></a>
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
            <div className="field__in">
              <Select options={this.getParsedTownOptions()} valueLink={this.linkState('parsedTown')} />
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
