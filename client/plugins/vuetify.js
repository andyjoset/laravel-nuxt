import 'vuetify/styles'
// import this after install `@mdi/font` package
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import colors from 'vuetify/lib/util/colors'
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import * as labs from 'vuetify/labs/components'
import { en, es } from 'vuetify/locale'

export default defineNuxtPlugin((nuxtApp) => {
    const vuetify = createVuetify({
        ssr: false,
        customVariables: ['~/assets/variables.scss'],
        theme: {
            defaultTheme: 'lightTheme',
            themes: {
                darkTheme: {
                    dark: true,
                    colors: {
                        primary: '#009E72', // colors.blue.darken2,
                        accent: colors.grey.darken3,
                        secondary: colors.amber.darken3,
                        info: colors.teal.lighten1,
                        warning: colors.amber.base,
                        error: colors.deepOrange.accent4,
                        success: colors.green.accent3,
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
        components: {
            ...labs,
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
