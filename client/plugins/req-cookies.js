export default ({ req }, inject) => {
    const helpers = {}
    const cookies = req.headers.cookie
        ? req.headers.cookie.split('; ').reduce((items, item) => {
            const [name, value] = item.split('=')
            return Object.assign(items, { [`${name}`]: value })
        }, {})
        : {}

    helpers.has = function (key) {
        return Boolean(cookies[key])
    }

    helpers.get = function (key) {
        return cookies[key]
    }

    helpers.all = function () {
        return cookies
    }

    inject('reqCookies', helpers)
}
