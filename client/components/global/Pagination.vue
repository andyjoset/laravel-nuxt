<template>
    <v-row class="pb-2 mt-0 mb-2 mx-auto">
        <template v-if="!isXs()">
            <v-col cols="6" class="my-auto">
                <span
                    v-if="config.from"
                    class="text-caption text-no-wrap text--secondary"
                    v-t="{ path: '$vuetify.dataFooter.pageText', args: [config.from, config.to, config.total] }" />
            </v-col>
        </template>

        <v-col xs="12" sm="6">
            <v-pagination
                v-bind="$attrs"
                :length="config.last_page"
                next-icon="mdi-menu-right"
                prev-icon="mdi-menu-left"
                :total-visible="7"
                color="primary"
                @input="paginate"
                v-on="$listeners" />

            <v-row v-if="isXs() && config.from" justify="center" class="mt-1 mb-1">
                <small
                    class="text-caption text--secondary"
                    v-t="{ path: '$vuetify.dataFooter.pageText', args: [config.from, config.to, config.total] }" />
            </v-row>
        </v-col>
    </v-row>
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
