module.exports = {
  env: {
    browser: true
  },
  parser: "@typescript-eslint/parser",
  plugins: [
    "@typescript-eslint"
  ],
  parserOptions: {
    sourceType: "module"
  },
  rules: {
    "@typescript/no-empty-interface": 0,
    "quotes": ["error", "double"],
    "quote-props": ["warn", "as-needed"],
    "arrow-parens": ["error", "as-needed"],
    "comma-dangle": ["error", "only-multiline"],
    "max-len": ["error", 120]
  }
}

