module.exports = {
  root: true,
  env: {
    node: true
  },
  extends: ['plugin:vue/essential', 'eslint:recommended', '@vue/prettier'],
  parserOptions: {
    parser: 'babel-eslint'
  },
  rules: {
    semi: [2, 'never'],
    'vue/multi-word-component-names': [
      'error',
      {
        ignores: ['Cabinet', 'Login']
      }
    ],
    'prettier/prettier': [
      'warn',
      {
        singleQuote: true,
        semi: false,
        trailingComma: 'none',
        arrowParens: 'avoid',
        endOfLine: 'auto'
      }
    ]
  }
}
