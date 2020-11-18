const path = require('path');
const config = require('../_config');
const rootFolder = path.resolve(__dirname, '../../');

module.exports = {
  target: 'web',
  context: path.resolve(rootFolder, config.input),
  output: {
    filename: '[name].js',
    path: path.join(rootFolder, config.output),
    publicPath: '/'
  }
};
