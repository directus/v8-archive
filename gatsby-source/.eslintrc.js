module.exports = {
  root: true,
  parser: '@typescript-eslint/parser',
  plugins: ['@typescript-eslint'],
  extends: [
    'eslint:recommended',
    'plugin:@typescript-eslint/eslint-recommended',
    'plugin:@typescript-eslint/recommended',
    'plugin:jest/recommended',
  ],
  rules: {
    '@typescript-eslint/naming-convention': ['error', { selector: 'variableLike', format: ['camelCase'] }],
    '@typescript-eslint/explicit-function-return-type': 'error',
  },
};
