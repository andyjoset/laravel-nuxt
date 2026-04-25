export default defineNuxtPlugin((nuxtApp) => {
    const helpers = {}
    const { cookie: cookies } = useRequestHeaders(['cookie'])

    helpers.has = function (key) {
        return Boolean(cookies[key])
    }

    helpers.get = function (key) {
        return cookies[key]
    }

    helpers.all = function () {
        return cookies
    }

    nuxtApp.provide('reqCookies', helpers)
})
