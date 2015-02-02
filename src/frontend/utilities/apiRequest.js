/**
 * cardiomagnyl
 */

var $ = require('jquery');

function apiRequest(method, url, data, callback) {
  if (!callback) {
    callback = data;
    data = null;
  }

  return $.ajax({
    data: data ? JSON.stringify({
      data: data
    }) : null,
    dataType: 'json',
    headers: {
      'Content-Type': 'application/vnd.api+json'
    },
    mimeType: 'application/vnd.api+json',
    type: method,
    url: url,
    success: function(data) {
      callback(null, data);
    },
    error: function(jqXhr) {
      callback(jqXhr);
    }
  });
}

module.exports = apiRequest;
