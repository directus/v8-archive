const gulp = require('gulp');
const autoprefixer = require('gulp-autoprefixer');
const cssnano = require('gulp-cssnano');
const gulpIf = require('gulp-if');
const gulpSass = require('gulp-sass');
const size = require('gulp-size');
const sourcemaps = require('gulp-sourcemaps');

const { isProd, input, output } = require('./_config');
const plumber = require('./_plumber');

const src = input + '/styles/**/*.scss';
const dest = output + '/styles';

function sass(callback) {
  return gulp.src(src)
    .pipe(plumber('Error Running Sass'))
    .pipe(sourcemaps.init())
    .pipe(gulpSass({ includePaths: ['./node_modules'] }))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write())
    .pipe(size({ title: 'styles' }))
    .pipe(gulpIf(isProd, cssnano()))
    .pipe(gulp.dest(dest));
}

exports.src = src;
exports.default = sass;
