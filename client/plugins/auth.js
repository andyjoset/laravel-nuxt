import Cookies from 'js-cookie'

export default function ({ app, $axios, $config, store, redirect }, inject) {
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

        await redirect({ name: 'login' })

        store.commit('TOGGLE_OVERLAY')
    }

    auth.updatePassword = function (form) {
        return form.put(`${this.baseUrl}/${this.api.updatePassword}`)
    }

    auth.requestEmailVerificationNotification = function () {
        return $axios.post(`${this.baseUrl}/${this.api.emailVerificationNotification}`)
    }

    auth.updateProfileInformation = async function (form) {
        await form.put(`${this.baseUrl}/${this.api.updateProfileInformation}`)

        store.commit('auth/UPDATE_USER', form.data())
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

            const expires = new Date()
            expires.setSeconds(expires.getSeconds() + data.expires)

            Cookies.set('token', data.token, { expires })

            await store.dispatch('auth/fetchUser')
        } else {
            store.commit('auth/SET_USER', data)
        }

        localStorage.loggedOut = false

        await app.router.replace(app.router.currentRoute.query.redirect || { name: 'dashboard' })

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

    /**
     * Check if the user has the given role.
     *
     * @param  string  role
     * @return bool
     */
    auth.hasRole = function (role) {
        try {
            return store.getters['auth/user'].roles.includes(role)
        } catch (e) {
            return false
        }
    }

    /**
     * Check if the user has any of the given roles.
     *
     * @param  array  roles
     * @return bool
     */
    auth.hasAnyRole = function (roles) {
        for (const role of roles) {
            if (this.hasRole(role)) {
                return true
            }
        }

        return false
    }

    /**
     * Check if the user has the given permission.
     *
     * @param  string  permission
     * @param  bool  checkIfIsAdmin
     * @return bool
     */
    auth.hasPermissionTo = function (permission, checkIfIsAdmin = true) {
        if (checkIfIsAdmin && this.hasRole('Super Admin')) {
            return true
        }

        try {
            return store.getters['auth/user'].permissions.includes(permission)
        } catch (e) {
            return false
        }
    }

    /**
     * Check if the user has any of the given permissions.
     *
     * @param  array  permissions
     * @param  bool  checkIfIsAdmin
     * @return bool
     */
    auth.hasAnyPermission = function (permissions, checkIfIsAdmin = true) {
        for (const permission of permissions) {
            if (this.hasPermissionTo(permission, checkIfIsAdmin)) {
                return true
            }
        }

        return false
    }

    auth.baseUrl = isStateful ? appUrl : apiUrl
    auth.api = {
        register: 'register',
        login: 'login' + (isStateful ? '' : '/token'),
        logout: 'logout' + (isStateful ? '' : '/token'),
        forgotPassword: 'forgot-password',
        resetPassword: 'reset-password',
        updatePassword: 'user/password',
        updateProfileInformation: 'user/profile-information',
        emailVerificationNotification: 'email/verification-notification',
        csrf: 'sanctum/csrf-cookie',
    }

    inject('auth', auth)
}
