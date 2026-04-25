import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import { en, es } from 'vuetify/locale'

export default defineNuxtPlugin((nuxtApp) => {
    const vuetify = createVuetify({
        ssr: false,
        defaults: {
            VBtn: {
                class: 'text-uppercase',
            },
        },
        theme: {
            defaultTheme: 'darkTheme',
            themes: {
                darkTheme: {
                    dark: true,
                    colors: {
                        primary: '#009E72',
                        accent: '#424242',
                        secondary: '#FFD54F',
                        info: '#26A69A',
                        warning: '#FFC107',
                        error: '#FF3D00',
                        success: '#00E676',
                    },
                },
                lightTheme: {
                    dark: false,
                    colors: {
                        primary: '#009E72',
                    },
                },
            }
        },
        icons: {
            defaultSet: 'mdi',
            aliases,
            sets: {
                mdi,
            }
        },
        locale: {
            locale: process.env.APP_LOCALE,
            fallback: 'en',
            messages: { en, es },
        },
    })

    nuxtApp.provide('vuetify', vuetify)
    nuxtApp.vueApp.use(vuetify)
})
