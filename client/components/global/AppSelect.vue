<template>
    <v-select
        v-bind="$attrs"
        :items="items"
        :loading="$fetchState.pending"
        v-on="$listeners">
        <template v-if="isServerSide" #append-item>
            <div v-intersect="onIntersetct" />

            <slot v-if="$fetchState.pending" name="loading">
                <v-divider class="mb-2" />
                <v-list-item :disabled="false" ripple>
                    <v-list-item-content>
                        <v-list-item-title>
                            <v-progress-circular indeterminate size="16" width="2" class="mx-1" />
                            {{ $t('loading') }}...
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </slot>
        </template>
    </v-select>
</template>

<script>
    export default {
        name: 'AppSelect',

        props: {
            serverAction: {
                type: String,
                default: () => undefined,
            },
        },

        data: () => ({
            pagination: {},
            serverItems: [],
        }),

        async fetch () {
            if (this.isServerSide) {
                await this.fetchItems()
            }
        },

        computed: {
            items () {
                if (this.$attrs.items) {
                    return this.$attrs.items
                }

                const itemText = this.$attrs['item-text'] || 'text'
                const itemValue = this.$attrs['item-value'] || 'value'

                const items = this.$attrs.multiple !== undefined
                    ? []
                    : [{ [`${itemText}`]: `-- ${this.$t('labels.select')} --`, [`${itemValue}`]: null }]

                return items.concat(this.serverItems.map(item =>
                    ({ [`${itemText}`]: item[itemText], [`${itemValue}`]: item[itemValue] })
                ))
            },
            isServerSide () {
                return Boolean(this.serverAction)
            },
        },

        methods: {
            async fetchItems () {
                if (this.pagination.current_page &&
                    this.pagination.current_page === this.pagination.last_page) {
                    return
                }

                this.$emit('fetching', { status: 'fetching' })

                try {
                    const params = { page: (this.pagination.current_page || 0) + 1 }
                    const data = await this.$axios.$get(this.serverAction, { params })

                    this.pagination = data.meta
                    this.serverItems.push(...data.data)

                    this.$emit('fetching', { status: 'success' }, data)
                } catch (e) {
                    this.$emit('fetching', { status: 'failed' }, e?.response || e)
                }
            },
            onIntersetct (entries, observer, isIntersecting) {
                if (isIntersecting && !this.$fetchState.pending) {
                    this.fetchItems()
                }
            },
        },
    }
</script>
