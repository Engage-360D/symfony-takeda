/**
 * AttrEactive Auth
 */

var authorizationStorage = {
  write: function(payload) {
    localStorage.setItem('takeda-auth/payload', JSON.stringify({
      token: payload && payload.data.id,
      userFullName: payload && payload.linked.users[0].firstname
    }));
  },

  read: function() {
    var item = localStorage.getItem('takeda-auth/payload');
    if (!item) return null;
    return JSON.parse(item);
  },

  clean: function() {
    localStorage.removeItem('takeda-auth/payload');
  }
};

module.exports = authorizationStorage;
