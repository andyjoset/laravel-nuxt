export default defineNuxtRouteMiddleware((to, from) => {
    const { $auth } = useNuxtApp()
    const permission = to.matched.find(r => r.name === to.name).meta.permission

    if (!permission) {
        return true
    }

    const isAllowed = Array.isArray(permission) ? $auth.hasAnyPermission(permission) : $auth.hasPermissionTo(permission)

    return isAllowed || navigateTo('/')
})
