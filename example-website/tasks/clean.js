const config = require('./_config');
const del = require('del');

function clean() {
  return del([`${config.output}/**/*`]);
}

module.exports = clean;
