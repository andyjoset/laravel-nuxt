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
    })

    $axios.onError((error) => {
        const status = error.response.status
        const auth = store.getters['auth/check']

        if (status === 401) {
            store.commit('auth/CLEAR')

            return redirect({ name: 'login', query: { redirect: app.router.currentRoute.fullPath } })
        }

        if (status === 403) {
            app.$swal.warning({
                title: 'Unauthorized!',
                text: error.response.data.message,
            }).then(() => {
                redirect(auth ? { name: 'dashboard' } : '/')
            })
        }

        Promise.reject(error)
    })
}
