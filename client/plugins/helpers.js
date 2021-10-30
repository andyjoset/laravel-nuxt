import Vue from 'vue'

/**
 * @see https://nuxtjs.org/docs/2.x/directory-structure/plugins#global-mixins
 */
if (!Vue.__global_mixin__) {
    Vue.__global_mixin__ = true

    Vue.mixin({
        methods: {
            $notify (options = {}) {
                if (typeof options === 'string') {
                    options = { message: options }
                }

                const config = {
                    message: '',
                    timeout: 10000,
                    outlined: false,
                    color: 'success',
                    ...options
                }

                this.$store.commit('SHOW_SNACKBAR_MESSAGE', config)
            },

            toCurrency ({ number, prefix = '$ ', locale = 'en', config = {} }) {
                const formatter = Intl.NumberFormat(locale, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                    ...config
                })

                return prefix + formatter.format(parseFloat(number))
            },

            pluck (items, keyNames, join = false, separator = ', ', formatter = null) {
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
            },

            groupByKey (objs, key) {
                return objs.reduce((objectsByKeyValue, obj) => {
                    const value = [key].map(key => obj[key]).join('-')

                    objectsByKeyValue[value] = (objectsByKeyValue[value] || []).concat(obj)

                    return objectsByKeyValue
                }, {})
            },

            dateDiff (date1, date2, diff, absolute = true) {
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
            },

            /**
             * @param  string  role
             * @return bool
             */
            $hasRole (role) {
                return this.$auth.hasRole(role)
            },

            /**
             * @param  array  roles
             * @return bool
             */
            $hasAnyRole (roles) {
                return this.$auth.hasAnyRole(roles)
            },

            /**
             * @param  string  permission
             * @param  bool  checkIfIsAdmin
             * @return bool
             */
            $can (permission, checkIfIsAdmin = true) {
                return this.$auth.hasPermissionTo(permission, checkIfIsAdmin)
            },

            /**
             * @param  array  permissions
             * @param  bool  checkIfIsAdmin
             * @return bool
             */
            $canAny (permissions, checkIfIsAdmin = true) {
                return this.$auth.hasAnyPermission(permissions, checkIfIsAdmin)
            },

            acronym (str) {
                return str.match(/[A-Z]/g).join('')
            },

            str_limit (string = '', limit = 300, end = '...') {
                return string.length <= limit ? string : string.substr(0, limit) + end
            },

            str_to_date (value, locale = 'en', options = { year: 'numeric', month: 'numeric', day: 'numeric' }) {
                return new Date(value)
                    .toLocaleString(locale, options)
                    .replace(/ /g, '-')
                    .replace('.', '')
                    .replace(/-([a-z])/, x => '-' + x[1].toUpperCase())
            },

            /**
             * @see https://gist.github.com/codeguy/6684588
             */
            slugify (text, separator = '-') {
                return text.toString()
                    .normalize('NFD') // split an accented letter in the base letter and the acent
                    .replace(/[\u0300-\u036F]/g, '') // remove all previously split accents
                    .toLowerCase()
                    .trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w-]+/g, '')
                    .replace(/--+/g, separator)
            },

            async $goTo (route, eventName = 'navigating', eventData = {}) {
                this.$emit(eventName, { ...eventData, status: 'start' })

                try {
                    await this.$router.push(route)

                    this.$emit(eventName, { ...eventData, status: 'success' })
                } catch (e) {
                    this.$emit(eventName, { ...eventData, error: e, status: 'failed' })
                }
            },

            routeIs (route) {
                return this.$route.name === (typeof route === 'object' ? route.name : route)
            },

            screenIs (breakpoint) {
                return this.$vuetify.breakpoint[breakpoint]
            },

            isXs () {
                return this.screenIs('xs')
            },

            isSm () {
                return this.screenIs('sm')
            },

            isMd () {
                return this.screenIs('md')
            },

            isLg () {
                return this.screenIs('lg')
            },

            isXl () {
                return this.screenIs('xl')
            },
        },
    })
}
