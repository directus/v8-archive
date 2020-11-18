const env = process.env.NODE_ENV;
const isDev = env === 'development';
const isProd = env === 'production';

module.exports = {
  env,
  isDev,
  isProd,
  input: 'src',
  output: 'dist',
  scssDir: 'styles',
  jsDir: 'scripts',
  imageSizes: [400, 900, 1300]
};
