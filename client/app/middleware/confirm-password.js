export default defineNuxtRouteMiddleware(async (to, from) => {
    const { $auth } = useNuxtApp()
    const { confirmed } = await $auth.confirmedPasswordStatus()

    if (!confirmed) {
        return navigateTo({ name: 'password.confirm', query: { redirect: to.fullPath } })
    }
})
