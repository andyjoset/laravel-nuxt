import { useDisplay } from 'vuetify'

export default function useHelpers (emit) {
    const router = useRouter()
    const currentRoute = useRoute()
    const { $auth, $store } = useNuxtApp()
    const { name: screenName } = useDisplay()

    const isXs = computed(() => screenIs('xs'))
    const isSm = computed(() => screenIs('sm'))
    const isMd = computed(() => screenIs('md'))
    const isLg = computed(() => screenIs('lg'))
    const isXl = computed(() => screenIs('xl'))

    function $notify (options = {}) {
        if (typeof options === 'string') {
            options = { message: options }
        }

        const config = {
            message: '',
            timeout: 10000,
            color: 'success',
            variant: 'elevated',
            ...options
        }

        $store.showSnackbarMessage(config)
    }

    function blank (value) {
        if (Array.isArray(value)) {
            return value.length === 0
        }

        if (value instanceof Date) {
            return false
        }

        if (typeof value === 'object' && !(value instanceof File) && value !== null) {
            return Object.keys(value).length === 0
        }

        return ['', null, undefined].includes(value)
    }

    function filled (value) {
        return !blank(value)
    }

    function toCurrency ({ number, prefix = '$ ', locale = 'en', config = {} }) {
        const formatter = Intl.NumberFormat(locale, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
            ...config
        })

        return prefix + formatter.format(parseFloat(number))
    }

    function pluck (items, keyNames, join = false, separator = ', ', formatter = null) {
        const results = []
        const keys = keyNames.split('.')

        for (const item in items) {
            let result = items[item]

            for (const key of keys) {
                if (!Object.prototype.hasOwnProperty.call(result, key)) {
                    result = null
                    break
                }

                result = result[key]
            }

            if (result) {
                results.push(
                    typeof formatter === 'function' ? formatter(result) : result
                )
            }
        }

        return join ? results.join(separator) : results
    }

    function groupByKey (objs, key) {
        return objs.reduce((objectsByKeyValue, obj) => {
            const value = [key].map(key => obj[key]).join('-')

            objectsByKeyValue[value] = (objectsByKeyValue[value] || []).concat(obj)

            return objectsByKeyValue
        }, {})
    }

    function dateDiff (date1, date2, diff, absolute = true) {
        const diffCalc = {
            seconds: 1000,
            minutes: 1000 * 60,
            hours: 1000 * 60 * 60,
            days: 1000 * 60 * 60 * 24,
            weeks: 24 * 3600 * 1000 * 7,
            months: (a, b) => (a.getMonth() + 12 * a.getFullYear()) - (b.getMonth() + 12 * b.getFullYear()),
            years: (a, b) => a.getFullYear() - b.getFullYear(),
        }

        const toUtc = date => Date.UTC(
            date.getFullYear(),
            date.getMonth(),
            date.getDate(),
            date.getHours(),
            date.getMinutes(),
            date.getSeconds(),
            date.getMilliseconds()
        )

        const dt1 = date1 instanceof Date ? date1 : new Date(date1)
        const dt2 = date2 instanceof Date ? date2 : new Date(date2)

        const result = ['months', 'years'].includes(diff)
            ? diffCalc[diff](dt1, dt2)
            : Math.round((toUtc(dt1) - toUtc(dt2)) / diffCalc[diff])

        return absolute ? Math.abs(result) : result
    }

    function addOrSubDate (date, time, qty, action, mutateOriginal = true) {
        const mlSeconds = {
            seconds: 1000,
            minutes: 1000 * 60,
            hours: 1000 * 60 * 60,
            days: 1000 * 60 * 60 * 24,
            weeks: 24 * 3600 * 1000 * 7,
        }

        const dateMlSeconds = date.getTime()
        const mlSecondsCalc = mlSeconds[time] * qty

        const result = action === 'add'
            ? new Date(dateMlSeconds + mlSecondsCalc)
            : new Date(dateMlSeconds - mlSecondsCalc)

        if (mutateOriginal) {
            date.setTime(result.getTime())
            return date
        }

        return result
    }

    /**
     * @param  string  role
     * @return bool
     */
    function $hasRole (role) {
        return $auth.hasRole(role)
    }

    /**
     * @param  array  roles
     * @return bool
     */
    function $hasAnyRole (roles) {
        return $auth.hasAnyRole(roles)
    }

    /**
     * @param  string  permission
     * @param  bool  checkIfIsAdmin
     * @return bool
     */
    function $can (permission, checkIfIsAdmin = true) {
        return $auth.hasPermissionTo(permission, checkIfIsAdmin)
    }

    /**
     * @param  array  permissions
     * @param  bool  checkIfIsAdmin
     * @return bool
     */
    function $canAny (permissions, checkIfIsAdmin = true) {
        return $auth.hasAnyPermission(permissions, checkIfIsAdmin)
    }

    function acronym (str) {
        return str.match(/[A-Z]/g).join('')
    }

    function str_limit (string = '', limit = 300, end = '...') {
        return string.length <= limit ? string : string.substr(0, limit) + end
    }

    function str_to_date (value, locale = 'en', options = { year: 'numeric', month: 'numeric', day: 'numeric' }) {
        return new Date(value)
            .toLocaleString(locale, options)
            .replace(/ /g, '-')
            .replace('.', '')
            .replace(/-([a-z])/, x => '-' + x[1].toUpperCase())
    }

    /**
     * @see https://gist.github.com/codeguy/6684588
     */
    function slugify (text, separator = '-') {
        return text.toString()
            .normalize('NFD') // split an accented letter in the base letter and the acent
            .replace(/[\u0300-\u036F]/g, '') // remove all previously split accents
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w-]+/g, '')
            .replace(/--+/g, separator)
    }

    async function $goTo (route, eventName = 'navigating', eventData = {}) {
        emit(eventName, { ...eventData, status: 'start' })

        try {
            await router.push(route)

            emit(eventName, { ...eventData, status: 'success' })
        } catch (e) {
            emit(eventName, { ...eventData, error: e, status: 'failed' })
        }
    }

    function routeIs (route) {
        return currentRoute.name === (typeof route === 'object' ? route.name : route)
    }

    function screenIs (breakpoint) {
        return breakpoint === screenName.value
    }

    return {
        $notify,
        blank,
        filled,
        toCurrency,
        pluck,
        groupByKey,
        dateDiff,
        addOrSubDate,
        $hasRole,
        $hasAnyRole,
        $can,
        $canAny,
        acronym,
        str_limit,
        str_to_date,
        slugify,
        $goTo,
        routeIs,
        screenIs,
        isXs,
        isSm,
        isMd,
        isLg,
        isXl,
    }
}
