const baseESLintConfig = require('./.eslintrc');

module.exports = Object.assign({}, baseESLintConfig, {
  env: {
    jest: true
  },
  plugins: ['jest']
});
