export default ({ $auth, redirect, route }) => {
    const permission = route.matched.find(r => r.name === route.name).meta.permission

    if (!permission) {
        return true
    }

    const isAllowed = Array.isArray(permission) ? $auth.hasAnyPermission(permission) : $auth.hasPermissionTo(permission)

    return isAllowed || redirect('/')
}
