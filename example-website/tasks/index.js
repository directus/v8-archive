const { series, parallel } = require('gulp');
const clean = require('./clean');
const eleventy = require('./eleventy');
const watch = require('./watch');
const rev = require('./rev');
const { serve } = require('./browser-sync');
const { default: sass } = require('./sass');
const { default: scripts } = require('./scripts');
const { default: images } = require('./images');

exports.clean = clean;

exports.default = series(
  clean,
  parallel(sass, eleventy, images),
  parallel(serve, watch)
);

exports.build = series(
  clean,
  parallel(sass, images, scripts),
  rev,
  eleventy
);
