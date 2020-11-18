const path = require('path');
const webpack = require('webpack');
const merge = require('webpack-merge');
const common = require('./webpack.common.js');
const entry = require('./webpack.entry.js');
const rootFolder = path.resolve(__dirname, '../../');
const { configureBabelLoader } = require('./utils');

const productionCommon = merge(common, {
  entry,
  optimization: { minimize: true },
  mode: 'production',
  devtool: 'source-map',
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'NODE_ENV': JSON.stringify('production')
      }
    }),
    new webpack.optimize.ModuleConcatenationPlugin()
  ],
  output: {
    path: path.resolve(rootFolder, 'dist/js')
  }
});

const legacyConfig = merge(productionCommon, {
  module: { rules: [configureBabelLoader('legacy')] },
  output: {
    filename: '[name]-legacy.js'
  }
});

const modernConfig = merge(productionCommon, {
  module: { rules: [configureBabelLoader('modern')] },
  output: {
    filename: '[name]-modern.js'
  }
});

module.exports = {
  legacyConfig,
  modernConfig
};
