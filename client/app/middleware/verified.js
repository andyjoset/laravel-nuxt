import { useAuthStore } from '~/store/auth'

export default defineNuxtRouteMiddleware((to, from) => {
    const authStore = useAuthStore()
    if (authStore.user?.email_verified_at === null) {
        return navigateTo({ name: 'dashboard' })
    }
})
