import { useAuthStore } from '~/store/auth'

export default defineNuxtPlugin(async (nuxtApp) => {
    const token = useCookie('token')
    const authStore = useAuthStore()

    if (token.value) {
        authStore.setToken(token.value)
    }
})
