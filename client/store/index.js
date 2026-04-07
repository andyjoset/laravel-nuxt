import Cookies from 'js-cookie'
import { defineStore } from 'pinia'
import { useAuthStore } from '~/store/auth'

export const useMainStore = defineStore('main', {
    state: () => ({
        snackbar: {
            message: '',
            color: '',
            timeout: 5000,
            variant: 'elevated',
        },
        overlay: {
            show: false,
        },
    }),

    actions: {
        showSnackbarMessage (config = {}) {
            Object.assign(this.snackbar, config)
        },

        toggleOverlay (options = {}) {
            Object.assign(this.overlay, {
                ...options,
                show: !this.overlay.show,
            })
        },

        nuxtClientInit ({ $config }) {
            const authStore = useAuthStore()
            if (!$config.public.isStateful) {
                const token = Cookies.get('token')

                if (token) {
                    authStore.setToken(token)
                }
            }
        }
    },
})

export default useMainStore
