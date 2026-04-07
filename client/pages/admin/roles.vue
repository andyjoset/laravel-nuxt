<template>
    <app-data-table
        name="roles"
        class="mt-12"
        :items="data.data"
        item-name="role"
        :headers="headers"
        :loading="pending"
        :heading="$t('roles', 2)"
        :pagination="data.meta"
        :server-action="serverAction"
        :action-create="actionCreate"
        :actions-validations="actionsValidations"
        :dialog-form-props="{ 'max-width': '700px' }"
        dialog-form-component-path="admin/forms/Role"
        @paginating="onPaginate"
        @item-created="onItemCreated"
        @item-updated="onItemUpdated"
        @item-deleted="onItemDeleted">
        <template #[`item.permissions`]="{ item: role }">
            <span v-text="pluck(role.permissions, 'description', true) || 'N/A'" />
        </template>
    </app-data-table>
</template>

<script setup>
    import useHelpers from '~/composables/helpers'
    import useDataTable from '~/composables/data-table'

    const { t } = useI18n()
    const { pluck } = useHelpers()
    const { $axios } = useNuxtApp()
    const route = useRoute()

    useHead({
        title: t('roles', 2),
    })

    const serverAction = ref('/admin/roles')
    const {
        setData,
        onPaginate,
        onItemCreated,
        onItemUpdated,
        onItemDeleted,
    } = useDataTable(serverAction)

    const { data, pending } = await useAsyncData('roles', () => $axios.$get(serverAction.value, { params: {
        s: route.query.s,
        page: route.query.page,
    }}), { watch: [
        () => route.query.s,
        () => route.query.page,
    ]})

    const actionsValidations = ref({
        update: {
            callback: role => role.name !== 'Super Admin',
        },
        delete: {
            callback: role => role.name !== 'Super Admin',
        },
    })

    const headers = computed(() => [
        { title: t('id'), key: 'id', sortable: true },
        { title: t('name'), key: 'name', sortable: true },
        { title: t('permissions', 2), key: 'permissions', sortable: true },
    ])

    const actionCreate = computed(() => ({
        text: t('create', [t('roles')]),
    }))

    watch(data, (value) => {
        setData(value)
    }, { immediate: true })
</script>
