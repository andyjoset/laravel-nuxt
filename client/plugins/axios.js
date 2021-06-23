export default function ({ $axios, $config, store, redirect }) {
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

        if (status === 401) {
            localStorage.loggedOut = true

            return redirect({ name: 'login' })
        }

        Promise.reject(error)
    })
}
