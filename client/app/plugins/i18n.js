export default defineNuxtPlugin((nuxtApp) => {
    // called right before setting a new locale
    nuxtApp.hook('i18n:beforeLocaleSwitch', ({ oldLocale, newLocale, initialSetup, context }) => {
        // nuxtApp.$vuetify.locale.current.value = newLocale
    })

    // called right after a new locale has been set
    nuxtApp.hook('i18n:localeSwitched', ({ oldLocale, newLocale }) => {
        //
    })
})
