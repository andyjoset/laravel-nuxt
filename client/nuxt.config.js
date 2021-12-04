import colors from 'vuetify/es5/util/colors'

require('dotenv').config()

export default {
    // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
    ssr: false,

    srcDir: __dirname,

    // Global page headers: https://go.nuxtjs.dev/config-head
    head () {
        const config = this.$config ?? this.publicRuntimeConfig
        const attrs = {
            title: config.appName,
            titleTemplate: '%s - ' + config.appName,
            meta: [
                { charset: 'utf-8' },
                { name: 'viewport', content: 'width=device-width, initial-scale=1' },
                { hid: 'description', name: 'description', content: '' },
            ],
            link: [
                { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
            ],
        }

        /** @see https://i18n.nuxtjs.org/seo */
        if (this.$nuxtI18nHead) {
            const merge = require('deepmerge')
            return merge(attrs, this.$nuxtI18nHead({ addSeoAttributes: true }))
        }

        return attrs
    },

    publicRuntimeConfig: {
        appUrl: process.env.APP_URL,
        apiUrl: process.env.APP_URL + '/api',
        appName: process.env.APP_NAME || 'Laravel Nuxt',
        isMultiLang: process.env.MULTI_LANG,
        isStateful: process.env.SESSION_DOMAIN.includes(
            new URL(process.env.SPA_URL).hostname
        ),
    },

    // Global CSS: https://go.nuxtjs.dev/config-css
    css: [
        { src: '~/assets/app.scss', lang: 'scss' },
    ],

    // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
    plugins: [
        { src: '~/plugins/req-cookies', mode: 'server' },
        '~/plugins/i18n',
        '~/plugins/axios',
        '~/plugins/vform',
        '~/plugins/auth',
        '~/plugins/helpers',
        '~/plugins/sweetalert',
        { src: '~/plugins/nuxtClientInit.js', mode: 'client' },
    ],

    // Auto import components: https://go.nuxtjs.dev/config-components
    components: [
        { path: '~/components/global' }
    ],

    // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
    buildModules: [
        // https://github.com/nuxt-community/router-module
        '@nuxtjs/router',

        // https://go.nuxtjs.dev/eslint
        '@nuxtjs/eslint-module',

        // https://go.nuxtjs.dev/vuetify
        '@nuxtjs/vuetify',
    ],

    // Modules: https://go.nuxtjs.dev/config-modules
    modules: [
        // https://i18n.nuxtjs.org
        '@nuxtjs/i18n',

        // https://go.nuxtjs.dev/axios
        '@nuxtjs/axios',

        // https://go.nuxtjs.dev/pwa
        // '@nuxtjs/pwa'
    ],

    loading: {
        color: '#009E72',
        continuous: true,
    },

    loadingIndicator: {
        name: 'fading-circle',
        background: '#121212',
    },

    router: {
        middleware: ['check-auth']
    },

    // PWA module configuration: https://go.nuxtjs.dev/pwa
    pwa: {
        manifest: {
            lang: 'en'
        }
    },

    // Vuetify module configuration: https://go.nuxtjs.dev/config-vuetify
    vuetify: {
        defaultAssets: false,
        customVariables: ['~/assets/variables.scss'],
        icons: {
            iconfont: 'mdi', // 'mdiSvg' || 'mdiSvg' || 'md' || 'fa' || 'fa4' || 'faSvg'
        },
        theme: {
            dark: true,
            themes: {
                dark: {
                    primary: '#009E72', // colors.blue.darken2,
                    accent: colors.grey.darken3,
                    secondary: colors.amber.darken3,
                    info: colors.teal.lighten1,
                    warning: colors.amber.base,
                    error: colors.deepOrange.accent4,
                    success: colors.green.accent3
                },
                light: {
                    primary: '#009E72',
                },
            }
        },
    },

    i18n: {
        locales: [
            { code: 'en', iso: 'en-US', file: 'en.js' },
            { code: 'es', iso: 'es-ES', file: 'es.js' },
        ],
        lazy: true,
        langDir: '~/lang',
        strategy: 'no_prefix',
        defaultLocale: process.env.DEFAULT_LOCALE || 'en',
    },

    // Build Configuration: https://go.nuxtjs.dev/config-build
    build: {
        extractCSS: true,
        babel: {
            plugins: [
                '@babel/plugin-proposal-optional-chaining',
            ],
        },
    },
}
