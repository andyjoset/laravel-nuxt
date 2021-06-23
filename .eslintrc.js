module.exports = {
    root: true,
    env: {
        browser: true,
        node: true
    },
    parserOptions: {
        parser: '@babel/eslint-parser',
        requireConfigFile: false,
        sourceType: 'module',
    },
    extends: [
        '@nuxtjs',
        'plugin:nuxt/recommended',
    ],
    plugins: [
    ],
    // add your custom rules here
    rules: {
        'comma-dangle': ['error', 'only-multiline'],
        'template-curly-spacing': 'off',
        indent: ['error', 4],

        // Vue rules
        'vue/html-indent': ['error', 4, { baseIndent: 1 }],
        'vue/script-indent': ['error', 4, { baseIndent: 1 }],
        'vue/html-closing-bracket-newline': ['error', {
            singleline: 'never',
            multiline: 'never',
        }],
        'vue/max-attributes-per-line': 'off',
    },
    overrides: [
        {
            files: ['*.vue'],
            rules: {
                'indent': 'off'
            }
        }
    ]
}
