/**
 * cardiomagnyl
 */

var Promise = require('bluebird');
var $ = require('jquery');

function apiRequest(method, url, data) {
  return Promise.resolve($.ajax({
    data: data ? JSON.stringify({
      data: data
    }) : null,
    dataType: 'json',
    headers: {
      'Content-Type': 'application/vnd.api+json'
    },
    mimeType: 'application/vnd.api+json',
    type: method,
    url: url
  }));
}

module.exports = apiRequest;
