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

            routeIs (name) {
                return this.$route.name === name
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
