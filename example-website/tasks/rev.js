const path = require('path');
const gulp = require('gulp');
const rev = require('gulp-rev');
const through = require('through2');
const rename = require('gulp-rename');
const toCamelCase = require('to-camel-case');
const { input, output } = require('./_config');

function initialRev() {
  return gulp.src([output + '/**/*.{css,js,mjs}'])
    .pipe(rev())
    .pipe(gulp.dest(output))
    .pipe(rev.manifest())
    .pipe(rename('rev.json'))
    .pipe(gulp.dest(output));
}

function modifyRevPaths() {
  return through.obj(function (file, enc, callback) {
    let manifest = JSON.parse(file.contents.toString());

    let r = Object.keys(manifest).reduce((acc, curr) => {
      const extname = path.extname(curr);
      const filename = path.basename(curr, extname);
      const formatted = toCamelCase(filename);
      acc[formatted] = manifest[curr];
      return acc;
    }, {});

    file.contents = Buffer.from((JSON.stringify(r, null, 2)));

    this.push(file);
    callback();
  });
}

function modifyRev() {
  return gulp.src(output + '/rev.json')
    .pipe(modifyRevPaths())
    .pipe(gulp.dest(input + '/_data/'));
}

const reviseAssets = gulp.series(initialRev, modifyRev);
module.exports = reviseAssets;
