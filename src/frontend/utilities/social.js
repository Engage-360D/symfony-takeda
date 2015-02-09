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

module.exports.openVk = openVk;
module.exports.openFb = openFb;
