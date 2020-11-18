const { src, dest, series, parallel } = require('gulp');
const imagemin = require('gulp-imagemin');
const newer = require('gulp-newer');

const { input, output } = require('./_config');

const imageInput = `${input}/images/**/*`;
const tmpOutput = './_tmp/minified';
const imageOutput = `${output}/images`;

function minifyImages() {
  return src(imageInput + `.{png,jpg,jpeg,gif}`)
    .pipe(newer(tmpOutput))
    .pipe(imagemin())
    .pipe(dest(tmpOutput));
}

function copyImagesToDist() {
  return src(tmpOutput + '/**/*')
    .pipe(dest(imageOutput));
}

function copySvgToDist() {
  return src(imageInput + '.svg')
    .pipe(dest(imageOutput));
}

function copyFaviconsToDist() {
  return src(input + '/favicons/**/*')
    .pipe(dest(imageOutput + '/favicons'));
}

const images = series(
  parallel(minifyImages),
  parallel(copyImagesToDist, copySvgToDist, copyFaviconsToDist)
);

exports.default = images;
exports.src = imageInput;
