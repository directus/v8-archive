const plumber = require('gulp-plumber');
const notify = require('gulp-notify');

function customPlumber(error) {
  return plumber({
    errorHandler: notify.onError({
      title: error || 'Error running Gulp',
      message: 'Error: <%= error.stack %>'
    })
  });
}

module.exports = customPlumber;
