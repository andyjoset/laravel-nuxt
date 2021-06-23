export default ({ store, redirect, route }) => {
    if (!store.getters['auth/check']) {
        return redirect({ name: 'login', query: { redirect: route.fullPath } })
    }
}
