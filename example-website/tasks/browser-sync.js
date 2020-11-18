const browserSync = require('browser-sync');
const server = browserSync.create();

const webpack = require('webpack');
const webpackDevMiddleware = require('webpack-dev-middleware');
const webpackConfig = require('./webpack/webpack.dev.js');
const compiler = webpack(webpackConfig);
const devMiddlewareOptions = {
  logLevel: 'warn',
  publicPath: webpackConfig.output.publicPath,
  stats: {
    colors: true,
    chunks: false
  }
};

function reload(done) {
  server.reload();
  done();
}

function serve(done) {
  server.init({
    open: false,
    server: './dist',
    middleware: [
      webpackDevMiddleware(compiler, devMiddlewareOptions)
    ]
  });
  done();
}

exports.reload = reload;
exports.serve = serve;
