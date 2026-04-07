// @ts-check
import withNuxt from './.nuxt/eslint.config.mjs'
import globals from 'globals'

export default withNuxt({
    languageOptions: {
        globals: {
            ...globals.browser,
            ...globals.node,
        },
        sourceType: 'module',
    },

    extends: [
    ],

    rules: {
        'indent': ['error', 4],
        'no-unused-vars': ['error', {
            'args': 'none',
            'caughtErrors': 'none',
        }],
        '@typescript-eslint/no-unused-vars': ['error', {
            'args': 'none',
            'caughtErrors': 'none',
        }],
        'no-empty': ['error', { 'allowEmptyCatch': true }],
        'comma-dangle': ['error', 'only-multiline'],
        'template-curly-spacing': 'off',
        'vue/html-indent': ['error', 4, {
            baseIndent: 1,
        }],
        'vue/script-indent': ['error', 4, {
            baseIndent: 1,
        }],
        'vue/html-closing-bracket-newline': ['error', {
            singleline: 'never',
            multiline: 'never',
        }],
        'vue/max-attributes-per-line': 'off',
        'vue/singleline-html-element-content-newline': ['error', {
            ignores: ['v-icon'],
        }],
        'vue/multi-word-component-names': 'off',
        'vue/no-v-text-v-html-on-component': 'off',
    },
}, {
    files: ['**/*.vue'],
    rules: {
        'indent': 'off',
    },
})
