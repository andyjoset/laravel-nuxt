/* @vue/component */
export default {
    data: () => ({
        items: [],
        pagination: {},
        fetching: true,
    }),

    watchQuery: ['page', 's'],

    methods: {
        onItemCreated (data) {
            this.items.unshift(data)

            if (this.items.length > this.pagination.per_page) {
                if (this.pagination.current_page === this.pagination.last_page) {
                    this.pagination.last_page++
                }

                this.items.splice(this.items.length - 1, 1)
            }
        },
        onItemUpdated ({ item, data }) {
            Object.assign(item, data)
        },
        onItemDeleted ({ item, index }, message) {
            this.$notify(message ?? this.$t('alerts.deleted'))

            this.pagination.total--
            this.items.splice(index, 1)

            /* eslint-disable camelcase */
            const { current_page, last_page, per_page } = this.pagination

            if (current_page === last_page) {
                this.pagination.to--
            }

            if (current_page < last_page && this.items.length < per_page) {
                this.fetchItems(this.$route.query)
            } else if (!this.items.length && current_page > 1) {
                this.updateQueryString({ ...this.$route.query, page: current_page - 1 })
            }
            /* eslint-enable camelcase */
        },
        onPaginate ($event) {
            this.fetching = $event.status === 'start'

            if ($event.data?.data) {
                this.items = $event.data.data
                this.pagination = $event.data.meta
            }
        },
        async updateQueryString (keys) {
            const query = {}

            keys = Array.isArray(keys) ? keys : Array(...arguments)

            for (const key of keys) {
                if (typeof key === 'object') {
                    Object.assign(query, key)
                } else {
                    query[key] = this[key] || undefined
                }
            }

            this.fetching = true

            try {
                await this.$router.push({ name: this.$route.name, query })
            } catch (e) {}
        },
        async fetchItems (params) {
            this.fetching = true

            try {
                const data = await this.$axios.$get(this.serverAction, { params })

                this.items = data.data
                this.pagination = data.meta
            } catch (e) {
            }

            this.fetching = false
        },
    },
}
