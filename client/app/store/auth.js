import Cookies from 'js-cookie'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: null,
    }),

    getters: {
        check: state => Boolean(state.user),
    },

    actions: {
        setUser (user) {
            this.user = user
        },
        setToken (token) {
            this.token = token
        },
        clear () {
            this.$reset()
            const { $axios, $config } = useNuxtApp()

            if (import.meta.client) {
                localStorage.loggedOut = true
            }

            if (!$config.public.isStateful) {
                Cookies.remove('token')
                delete $axios.defaults.headers.common.Authorization
            }
        },
        updateUser (user) {
            Object.assign(this.user, user)
        },
        async fetchUser () {
            try {
                const { $axios } = useNuxtApp()
                const user = await $axios.$get('/user')

                this.setUser(user)
            } catch (e) {
                this.clear()
            }
        },
    },
})
