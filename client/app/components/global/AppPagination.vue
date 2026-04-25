<template>
    <v-row class="pb-2 mt-0 mb-2 mx-auto">
        <template v-if="!isXs">
            <v-col cols="6" class="my-auto text-left">
                <span v-if="config.from" class="text-caption text-no-wrap text--secondary">
                    {{ $t('$vuetify.dataFooter.pageText', [config.from, config.to, config.total]) }}
                </span>
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
                @update:model-value="paginate" />

            <v-row v-if="isXs && config.from" class="justify-center mt-1 mb-1">
                <small class="text-caption text--secondary">
                    {{ $t('$vuetify.dataFooter.pageText', [config.from, config.to, config.total]) }}
                </small>
            </v-row>
        </v-col>
    </v-row>
</template>

<script setup>
    import useHelpers from '~/composables/helpers'

    defineOptions({
        inheritAttrs: false,
    })

    const emit = defineEmits(['paginating'])
    const props = defineProps({
        config: {
            type: Object,
            required: true,
        },
        appendQuery: {
            type: Object,
            default: () => ({}),
        },
        useRouter: {
            type: Boolean,
            default: true,
        },
    })

    const route = useRoute()
    const { isXs, $goTo } = useHelpers(emit)

    async function paginate (page) {
        page = isNaN(page) ? 1 : page

        if (!props.useRouter) {
            return
        }

        const query = { ...route.query, page }

        for (const key in props.appendQuery) {
            query[key] = props.appendQuery[key] || undefined
        }

        await $goTo({ name: route.name, query }, 'paginating')
    }
</script>
