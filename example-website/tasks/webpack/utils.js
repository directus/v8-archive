const configureBabelLoader = env => {
  if (env !== 'dev' && env !== 'modern' && env !== 'legacy') {
    throw Error('configureBabelLoader can only take in three envs: dev, modern or legacy. Please change env accordingy');
  }

  const base = {
    test: /\.js$/,
    exclude: /node_modules/,
    use: {
      loader: 'babel-loader',
      options: {
        presets: ['env'],
        plugins: []
      }
    }
  };

  const legacyBrowsersList = [
    '> 1%',
    'last 2 versions',
    'Firefox ESR'
  ];

  const modernBrowsersList = [
    'last 2 Chrome versions', 'not Chrome < 60',
    'last 2 Safari versions', 'not Safari < 10.1',
    'last 2 iOS versions', 'not iOS < 10.3',
    'last 2 Firefox versions', 'not Firefox < 54',
    'last 2 Edge versions', 'not Edge < 15'
  ];

  if (env === 'dev') {
    return base;
  }

  if (env === 'legacy') {
    base.use.options.presets[0] = ['@babel/env', {
      targets: { browsers: legacyBrowsersList }
    }];
    return base;
  }

  if (env === 'modern') {
    base.use.options.presets[0] = ['@babel/env', {
      targets: { browsers: modernBrowsersList }
    }];
    return base;
  }
}

module.exports = {
  configureBabelLoader
};
