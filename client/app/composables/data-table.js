import useHelpers from '~/composables/helpers'

export default function useDataTable (serverAction) {
    const { t } = useI18n()
    const route = useRoute()
    const router = useRouter()
    const { $axios } = useNuxtApp()
    const { $notify } = useHelpers()

    const items = ref([])
    const pagination = ref({})

    function onItemCreated (item) {
        items.value.unshift(item)
        pagination.value.total++

        if (items.value.length > pagination.value.per_page) {
            if (pagination.value.current_page === pagination.value.last_page) {
                pagination.value.last_page++
            }

            items.value.splice(items.value.length - 1, 1)
        }
    }

    function onItemUpdated ({ item, data }) {
        Object.assign(item, data)
    }

    function onItemDeleted ({ item, index }, message) {
        $notify(message ?? t('alerts.deleted'))

        pagination.value.total--
        items.value.splice(index, 1)

        const { current_page, last_page, per_page } = pagination.value

        if (current_page === last_page) {
            pagination.value.to--
        }

        if (current_page < last_page && items.value.length < per_page) {
            refreshNuxtData()
        } else if (!items.value.length && current_page > 1) {
            updateQueryString({ ...route.query, page: current_page - 1 })
        }
    }

    function onPaginate ($event) {
        if ($event.data?.data) {
            items.value = $event.data.data
            pagination.value = $event.data.meta
        }
    }

    async function updateQueryString (keys) {
        const query = {}

        keys = Array.isArray(keys) ? keys : Array(...arguments)

        for (const key of keys) {
            if (typeof key === 'object') {
                Object.assign(query, key)
            } else {
                query[key] = this[key] || undefined
            }
        }

        try {
            await router.push({ name: route.name, query })
        } catch (e) {}
    }

    async function fetchItems (params) {
        try {
            const data = await $axios.$get(serverAction.value, { params })

            items.value = data.data
            pagination.value = data.meta

            return data
        } catch (e) {
            return e
        }
    }

    function setData (data) {
        items.value = data.data
        pagination.value = data.meta
    }

    return {
        setData,
        fetchItems,
        updateQueryString,
        onItemCreated,
        onItemUpdated,
        onItemDeleted,
        onPaginate,
    }
}
