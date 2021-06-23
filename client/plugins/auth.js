import Cookies from 'js-cookie'

export default function ({ $axios, $config, store, redirect }, inject) {
    const auth = {}
    const { isStateful, appUrl, apiUrl } = $config

    auth.login = async function (form) {
        try {
            await this.csrf(form)

            const { data } = await form.post(`${this.baseUrl}/${this.api.login}`)

            await this.loggedIn(data)
        } catch (e) {
        }
    }

    auth.register = async function (form) {
        try {
            await this.csrf(form)

            const { data } = await form.post(`${this.baseUrl}/${this.api.register}`)

            await this.loggedIn(data)
        } catch (e) {
        }
    }

    auth.logout = async function () {
        store.commit('TOGGLE_OVERLAY')

        try {
            await $axios.post(`${this.baseUrl}/${this.api.logout}`)
        } catch (e) {
        }

        store.commit('auth/CLEAR')

        localStorage.loggedOut = true

        if (!isStateful) {
            Cookies.remove('token')
            delete $axios.defaults.headers.common.Authorization
        }

        await redirect({ name: 'login' })

        store.commit('TOGGLE_OVERLAY')
    }

    auth.forgotPassword = async function (form, type) {
        await this.csrf(form)

        return form.post(`${this.baseUrl}/${this.api[type + 'Password']}`)
    }

    auth.loggedIn = async function (data) {
        store.commit('TOGGLE_OVERLAY')

        // Already authenticated
        if (data.error) {
            await store.dispatch('auth/fetchUser')
        } else if (data.token) {
            store.commit('auth/SET_TOKEN', data.token)

            Cookies.set('token', data.token)

            await store.dispatch('auth/fetchUser')
        } else {
            store.commit('auth/SET_USER', data)
        }

        localStorage.loggedOut = false

        await redirect({ name: 'dashboard' })
        store.commit('TOGGLE_OVERLAY')
    }

    auth.csrf = function (form) {
        if (!isStateful || Cookies.get('XSRF-TOKEN')) {
            return
        }

        try {
            form?.startProcessing()
            return $axios.get(`${appUrl}/${this.api.csrf}`)
        } catch (e) {
            form?.handleErrors()
            return e
        }
    }

    auth.baseUrl = isStateful ? appUrl : apiUrl
    auth.api = {
        register: 'register',
        login: 'login' + (isStateful ? '' : '/token'),
        logout: 'logout' + (isStateful ? '' : '/token'),
        forgotPassword: 'forgot-password',
        resetPassword: 'reset-password',
        csrf: 'sanctum/csrf-cookie',
    }

    inject('auth', auth)
}
