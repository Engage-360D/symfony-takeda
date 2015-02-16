function capitaliseFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * In order to get an inflector of the noun "минута",
 * you should pass this array of options:
 * options = [
 *   "минута", // 1
 *   "минуты", // 2-4
 *   "минут"   // 5
 * ]
 *
 * @param n
 * @param options
 */
function getNounInflector(options) {
  return function (n) {
    n = n % 100;

    if (n > 10 && n < 20) {
      return options[2];
    } else if (n % 10 === 1) {
      return options[0];
    } else if (n % 10 > 1 && n % 10 < 5) {
      return options[1];
    } else {
      return options[2];
    }
  }
}

module.exports.capitaliseFirstLetter = capitaliseFirstLetter;
module.exports.getNounInflector = getNounInflector;