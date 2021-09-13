export default function ({ app, $vuetify }) {
    $vuetify.lang.t = (key, ...params) => app.i18n.t(key, params)

    app.i18n.onBeforeLanguageSwitch = (oldLocale, newLocale, isInitialSetup, context) => {
        $vuetify.lang.current = newLocale
    }

    app.i18n.onLanguageSwitched = (oldLocale, newLocale) => {
        //
    }
}
