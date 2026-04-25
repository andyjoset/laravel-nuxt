import Cookies from 'js-cookie'
import { useAuthStore } from '~/store/auth'

export default defineNuxtPlugin((nuxtApp) => {
    const auth = {}
    const authStore = useAuthStore()
    const { $axios } = useNuxtApp()
    const { isStateful, appUrl, apiUrl } = useRuntimeConfig().public

    auth.login = async function (form) {
        await this.csrf(form)

        const { data } = await form.post(`${this.baseUrl}/${this.api.login}`)

        await this.loggedIn(data)

        return data
    }

    auth.register = async function (form) {
        await this.csrf(form)

        const { data } = await form.post(`${this.baseUrl}/${this.api.register}`)

        await this.loggedIn(data)

        return data
    }

    auth.logout = async function () {
        nuxtApp.$store.toggleOverlay()

        try {
            await $axios.post(`${this.baseUrl}/${this.api.logout}`)
        } catch (e) {
        }

        authStore.clear()

        await navigateTo({ name: 'login' })

        nuxtApp.$store.toggleOverlay()
    }

    auth.updatePassword = function (form) {
        return form.put(`${this.baseUrl}/${this.api.updatePassword}`)
    }

    auth.requestEmailVerificationNotification = function () {
        return $axios.post(`${this.baseUrl}/${this.api.emailVerificationNotification}`)
    }

    auth.updateProfileInformation = async function (form) {
        await form.put(`${this.baseUrl}/${this.api.updateProfileInformation}`)

        authStore.updateUser(form.data())
    }

    auth.forgotPassword = async function (form, type) {
        await this.csrf(form)

        return form.post(`${this.baseUrl}/${this.api[type + 'Password']}`)
    }

    auth.confirmPassword = function (form) {
        return form.post(`${this.baseUrl}/${this.api.confirmPassword}`)
    }

    auth.confirmedPasswordStatus = function () {
        return $axios.$get(`${this.baseUrl}/${this.api.confirmedPasswordStatus}`)
    }

    auth.loggedIn = async function (data) {
        nuxtApp.$store.toggleOverlay()

        // Already authenticated
        if (data.error) {
            await authStore.fetchUser()
        } else if (data.token) {
            authStore.setToken(data.token)

            const expires = new Date()
            expires.setSeconds(expires.getSeconds() + data.expires)

            Cookies.set('token', data.token, { expires })

            await authStore.fetchUser()
        } else {
            authStore.setUser(data)
        }

        localStorage.loggedOut = false

        await nuxtApp.$router.replace(nuxtApp.$router.currentRoute.value.query.redirect || { name: 'dashboard' })

        nuxtApp.$store.toggleOverlay()
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
            return authStore.user.roles.includes(role)
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
            return authStore.user.permissions.includes(permission)
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
        confirmPassword: 'user/confirm-password',
        confirmedPasswordStatus: 'user/confirmed-password-status',
        updateProfileInformation: 'user/profile-information',
        emailVerificationNotification: 'email/verification-notification',
        csrf: 'sanctum/csrf-cookie',
    }

    nuxtApp.provide('auth', auth)
})
