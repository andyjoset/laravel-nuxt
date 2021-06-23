export default async ({ $axios, $config, store }) => {
    const token = store.getters['auth/token']
    const loggedIn = token || ($config.isStateful && !JSON.parse(localStorage.loggedOut || false))

    if (!store.getters['auth/check'] && loggedIn) {
        await store.dispatch('auth/fetchUser')
    }
}
