<template>
    <div>
        <v-pagination
            v-bind="$attrs"
            :length="config.last_page"
            prev-icon="mdi-menu-left"
            next-icon="mdi-menu-right"
            :total-visible="7"
            color="primary"
            class="pt-2"
            @input="paginate"
            v-on="$listeners" />

        <v-row v-if="config.from" justify="center" class="pb-2 mt-1 mb-2">
            <small class="text-caption">
                {{ config.from }} - {{ config.to }} of {{ config.total }}
            </small>
        </v-row>
    </div>
</template>

<script>
    export default {
        props: {
            config: {
                type: Object,
                required: true,
            },
            appendQuery: {
                type: Object,
                default: () => ({}),
            },
        },

        methods: {
            async paginate (page) {
                this.$emit('paginating', { status: 'start' })

                const query = { ...this.$route.query, page: isNaN(page) ? undefined : page }

                for (const key in this.appendQuery) {
                    query[key] = this.appendQuery[key] || undefined
                }

                try {
                    await this.$router.push({ name: this.$route.name, query })

                    this.$emit('paginating', { status: 'success' })
                } catch (e) {
                    this.$emit('paginating', { status: 'failed' })
                }
            },
        },
    }
</script>
