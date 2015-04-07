var React = require('react');
var $ = require('jquery');

function itemAddress(item) {
  return [
    item.parsedTown,
    item.parsedStreet,
    item.parsedHouse,
    item.parsedCorpus,
    item.parsedBuilding
  ].filter(function(i) { return !!i; }).join(', ');
}

var InstitutionsMap = React.createClass({
  statics: {
    minZoom: 3,
    maxZoom: 16
  },

  getInitialState: function() {
    return {
      zoom: 7
    };
  },

  componentDidUpdate: function(prevProps, prevState) {
    if (!this.map) return;

    if (this.state.zoom !== prevState.zoom) {
      this.map.setZoom(this.state.zoom);
    }
  },

  componentDidMount: function() {
    ymaps.load(function() {
      var node = this.refs.map.getDOMNode();

      this.map = new ymaps.Map(node, {
        center: [55.76, 37.64],
        zoom: this.state.zoom,
        controls: []
      });

      var target;
      if (this.props.results.length > 30) {
        target = new ymaps.Clusterer();
      } else {
        target = this.map.geoObjects;
      }

      this.props.results.forEach(function(item) {
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
        if (zoom > InstitutionsMap.maxZoom) {
          zoom = InstitutionsMap.maxZoom;
        }
        if (zoom < InstitutionsMap.minZoom) {
          zoom = InstitutionsMap.minZoom;
        }
        this.setState({zoom: zoom});
      }

      $(node).find('.ymaps-2-1-23-map').css({
        'background': 'transparent'
      });
      $(node).find('.ymaps-2-1-23-events-pane').css({
        'border-radius': '100%'
      });
      $(node).find('.ymaps-2-1-23-ground-pane').css({
        'width': '100%',
        'height': '100%',
        'border-radius': '100%',
        'overflow': 'hidden'
      });
      setTimeout(function() {
        $(node).find('.ymaps-2-1-23-places-pane').css({
          'width': '100%',
          'height': '100%',
          'border-radius': '100%',
          'overflow': 'hidden'
        });
      }, 10);
      $(node).find('.ymaps-2-1-23-copyright').css({
        'position': 'absolute',
        'top': '-45px',
        'left': '135px'
      });
    }.bind(this));
  },

  zoomIn: function() {
    var zoom = this.state.zoom + 1;
    if (zoom > InstitutionsMap.maxZoom) return;
    this.setState({zoom: zoom});
  },

  zoomOut: function() {
    var zoom = this.state.zoom - 1;
    if (zoom < InstitutionsMap.minZoom) return;
    this.setState({zoom: zoom});
  },

  render: function() {
    var position = 100 - ((this.state.zoom - InstitutionsMap.minZoom) / (InstitutionsMap.maxZoom - InstitutionsMap.minZoom) * 100);

    return (
      <div className="main__item main__item_3">
        <div className="main__item-in">
          <div className="main__map">
            <div className="h">Карта учреждений в регионе {this.props.parsedTown}</div>
            <a className="link" href="/institutions"><span>Полный список</span><i className="icon icon-arr-circle-right"></i><i className="icon icon-arr-circle-right-fill"></i></a>
          </div>
          <div className="map">
            <div className="map__panel">
              <div className="map__plus" onClick={this.zoomIn}>
                <i className="icon icon-plus-fill"></i>
                <i className="icon icon-plus"></i>
              </div>
              <ul className="map__zoom">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li className="map__zoom-drag" style={{top: Math.round(position) + '%'}}></li>
              </ul>
              <div className="map__minus" onClick={this.zoomOut}>
                <i className="icon icon-minus-fill"></i>
                <i className="icon icon-minus"></i>
              </div>
            </div>
            <div className="map__container">
              <div className="map__container-in" ref="map" style={{overflow: 'hidden'}}>
              </div>
              <i className="map__shadow"></i>
            </div>
          </div>
        </div>
      </div>
    );
  }
});

module.exports = InstitutionsMap;
