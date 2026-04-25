import { useAuthStore } from '~/store/auth'

export default defineNuxtRouteMiddleware(async (to, from) => {
    let loggedIn = false
    const authStore = useAuthStore()
    const { isStateful } = useRuntimeConfig().public

    const csrfToken = useCookie('XSRF-TOKEN').value
    if (import.meta.server) {
        loggedIn = Boolean(csrfToken || (!isStateful && authStore.token))
    } else {
        const loggedOut = JSON.parse(localStorage.loggedOut ?? false)
        loggedIn = isStateful
            ? !loggedOut && csrfToken
            : !loggedOut && authStore.token
    }

    if (!authStore.check && loggedIn) {
        await authStore.fetchUser()
    }
})
