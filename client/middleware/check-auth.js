import Cookies from 'js-cookie'

export default async ({ $auth, $axios, $config, $reqCookies, store }) => {
    let loggedIn = false
    if (process.server) {
        loggedIn = $reqCookies.has('XSRF-TOKEN') || (!$config.isStateful && store.getters['auth/token'])
    } else {
        const loggedOut = JSON.parse(localStorage.loggedOut ?? false)
        loggedIn = $config.isStateful
            ? !loggedOut && Cookies.get('XSRF-TOKEN')
            : !loggedOut && store.getters['auth/token']
    }

    if (!store.getters['auth/check'] && loggedIn) {
        await store.dispatch('auth/fetchUser')
    }
}
