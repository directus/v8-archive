const webpack = require('webpack')
const { jsDir } = require('../_config');
const merge = require('webpack-merge')
const common = require('./webpack.common.js')
const entry = require('./webpack.entry.js')
const { configureBabelLoader } = require('./utils')

const config = merge(common, {
  entry,
  mode: 'development',
  module: { rules: [configureBabelLoader('modern')] },
  devtool: 'cheap-eval-source-map',
  plugins: [
    new webpack.NamedModulesPlugin(),
    new webpack.DefinePlugin({
      'process.env': {
        'NODE_ENV': JSON.stringify('development')
      }
    })
  ]
});

module.exports = config
