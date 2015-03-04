function openVk(callback) {
  window.authDone = function() {
    callback();
  };
  window.open('/oauth/vkontakte');
}

function openFb(callback) {
  window.authDone = function() {
    callback();
  };
  window.open('/oauth/facebook');
}

function openOk(callback) {
  window.authDone = function() {
    callback();
  };
  window.open('/oauth/odnoklassniki');
}

function openGoogle(callback) {
  window.authDone = function() {
    callback();
  };
  window.open('/oauth/google');
}

module.exports.openVk = openVk;
module.exports.openFb = openFb;
module.exports.openOk = openOk;
module.exports.openGoogle = openGoogle;
