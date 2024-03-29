export default function ({ app, $axios, $config, store, redirect }) {
    $axios.setBaseURL($config.apiUrl)

    if ($config.isStateful) {
        $axios.defaults.withCredentials = true
    }

    $axios.onRequest((config) => {
        const token = store.getters['auth/token']
        if (token) {
            $axios.setToken(token, 'Bearer')
        }

        const locale = app.i18n.getLocaleCookie()
        if (locale) {
            $axios.setHeader('Accept-Language', locale)
        }

        if (process.server && $config.isStateful && !config.headers.common.referer) {
            $axios.setHeader('Referer', process.env.SPA_URL)
        }
    })

    $axios.onError((error) => {
        const status = error.response.status
        const auth = store.getters['auth/check']
        const currentPath = app.router.currentRoute.fullPath

        if (status === 401) {
            store.commit('auth/CLEAR')

            return redirect({ name: 'login', query: { redirect: currentPath } })
        }

        if (status === 403) {
            app.$swal.warning({
                title: app.i18n.t('alerts.unauthorized'),
                text: error.response.data.message,
            }).then(() => {
                redirect(auth ? { name: 'dashboard' } : '/')
            })
        }

        if (status === 423) {
            return redirect({ name: 'password.confirm', query: { redirect: currentPath } })
        }

        Promise.reject(error)
    })
}
