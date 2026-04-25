<template>
    <v-select
        v-bind="$attrs"
        :items="items"
        :loading="fetching">
        <template v-if="isServerSide" #append-item>
            <div v-intersect="onIntersetct" />

            <slot v-if="fetching" name="loading">
                <v-divider class="mb-2" />
                <v-list-item :disabled="false" ripple>
                    <v-list-item-title>
                        <v-progress-circular indeterminate size="16" width="2" class="mx-1" />
                        {{ $t('loading') }}...
                    </v-list-item-title>
                </v-list-item>
            </slot>
        </template>
    </v-select>
</template>

<script setup>
    const { t } = useI18n()
    const attrs = useAttrs()
    const { $axios } = useNuxtApp()

    const emit = defineEmits(['fetching', 'success', 'failed'])
    const props = defineProps({
        serverAction: {
            type: String,
            default: () => undefined,
        },
    })

    const fetching = ref(false)
    const pagination = ref({})
    const serverItems = ref([])

    const items = computed(() => {
        if (attrs.items) {
            return attrs.items
        }

        const itemText = attrs['item-title'] || 'title'
        const itemValue = attrs['item-value'] || 'value'

        const items = attrs.multiple !== undefined
            ? []
            : [{ [`${itemText}`]: `-- ${t('labels.select')} --`, [`${itemValue}`]: null }]

        return items.concat(serverItems.value.map(item =>
            ({ [`${itemText}`]: item[itemText], [`${itemValue}`]: item[itemValue] })
        ))
    })

    const isServerSide = computed(() => Boolean(props.serverAction))

    async function fetchItems () {
        if (pagination.value.current_page &&
            pagination.value.current_page === pagination.value.last_page) {
            return
        }

        emit('fetching', { status: 'fetching' })

        try {
            const params = { page: (pagination.value.current_page || 0) + 1 }
            const data = await $axios.$get(props.serverAction, { params })

            pagination.value = data.meta
            serverItems.value.push(...data.data)

            emit('fetching', { status: 'success' }, data)
        } catch (e) {
            emit('fetching', { status: 'failed' }, e?.response || e)
        }

        fetching.value = false
    }

    function onIntersetct (entries, observer, isIntersecting) {
        if (isIntersecting && !fetching.value) {
            fetchItems()
        }
    }

    onMounted(() => {
        if (isServerSide.value) {
            fetchItems()
        }
    })
</script>
