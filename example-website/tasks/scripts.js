const { parallel } = require('gulp');
const webpack = require('webpack');
const { input, jsDir } = require('./_config');
const { legacyConfig, modernConfig } = require('./webpack/webpack.prod.js');

function compile(config) {
  return new Promise(resolve => {
    webpack(config, (err, stats) => {
      if (err) console.log('Webpack', err);
      console.log(stats.toString({
        colors: true,
        chunks: false
      }));
      resolve();
    });
  });
}

function compileLegacy() {
  return compile(legacyConfig);
}

function compileModern() {
  return compile(modernConfig);
}

const scripts = parallel(compileModern, compileLegacy);

module.exports = {
  src: input + jsDir + '/**/*.js',
  default: scripts
};
