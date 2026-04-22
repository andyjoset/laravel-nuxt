import { join } from 'path'
import { cpSync, rmSync } from 'node:fs'
import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify'

import dotenv from 'dotenv'
dotenv.config()

const appName = process.env.APP_NAME || 'Laravel Nuxt'

export default defineNuxtConfig({
    ssr: false,
    devtools: { enabled: false },
    srcDir: 'client/',
    css: ['~/assets/app.scss'],

    experimental: {
        appManifest: false
    },

    components: {
        dirs: [
            { path: '~/components/global', global: true },
        ],
    },

    build: {
        transpile: ['vuetify'],
        extractCSS: true,
    },

    vite: {
        ssr: {
            noExternal: ['vuetify'],
        },
        define: {
            'process.env.DEBUG': false,
        },
        plugins: [
            // @ts-expect-error
            vuetify({ autoImport: true }),
        ],
        vue: {
            template: {
                transformAssetUrls,
            },
        },
    },

    plugins: [
        { src: '~/plugins/nuxtServerInit.js', mode: 'server' },
        { src: '~/plugins/req-cookies.js', mode: 'server' },
        '~/plugins/i18n',
        '~/plugins/vuetify',
        '~/plugins/pinia',
        '~/plugins/axios',
        '~/plugins/vform',
        '~/plugins/auth',
        '~/plugins/sweetalert',
        { src: '~/plugins/nuxtClientInit.js', mode: 'client' },
    ],

    modules: [
        (_options, nuxt) => {
            nuxt.hooks.hook('vite:extendConfig', (config) => {
            // @ts-expect-error
            config.plugins.push(vuetify({ autoImport: true }))
        })},
        '@nuxtjs/i18n',
        '@pinia/nuxt',
        '@vueuse/nuxt',
        '@nuxt/eslint',
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
        middleware: ['check-auth'],
    },

    routeRules: {
        '/admin/**': { ssr: false },
    },

    app: {
        head: {
            title: appName,
            titleTemplate: '%s - ' + appName,
            meta: [
                { charset: 'utf-8' },
                { name: 'viewport', content: 'width=device-width, initial-scale=1' },
                { hid: 'description', name: 'description', content: '' },
            ],
            link: [
                { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
            ],
        },
    },

    runtimeConfig: {
        // The private keys which are only available server-side
        // apiSecret: '123',
        public: {
            appName,
            appUrl: process.env.APP_URL,
            apiUrl: process.env.APP_URL + '/api',
            isMultiLang: process.env.MULTI_LANG,
            isStateful: process.env.SESSION_DOMAIN.includes(
                new URL(process.env.SPA_URL).hostname
            ),
        }
    },

    eslint: {
        checker: true,
    },

    i18n: {
        langDir: 'lang',
        restructureDir: 'client',
        strategy: 'no_prefix',
        locales: [
            { code: 'en', language: 'en-US', name: 'English', file: 'en.js' },
            { code: 'es', language: 'es-ES', name: 'Español', file: 'es.js' },
        ],
        baseUrl: process.env.APP_URL,
        defaultLocale: process.env.APP_LOCALE || 'en',
    },

    hooks: {
        'nitro:build:public-assets': (nitro) => {
            const options = nitro.options
            if (!options.dev && options.static) {
                cpSync(join(options.output.publicDir, '_nuxt'), join('public', '_nuxt'), { recursive: true })
                cpSync(join(options.output.publicDir, '200.html'), join('public', '_nuxt', 'index.html'))

                rmSync(options.output.dir, { recursive: true, force: true })
            }
        },
    },

    compatibilityDate: '2026-04-07',
})