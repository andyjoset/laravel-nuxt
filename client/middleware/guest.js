import { useAuthStore } from '~/store/auth'

export default defineNuxtRouteMiddleware((to, from) => {
    const authStore = useAuthStore()
    if (authStore.check) {
        return navigateTo({ name: 'dashboard' })
    }
})
