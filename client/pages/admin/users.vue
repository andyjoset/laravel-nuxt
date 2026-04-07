<template>
    <app-data-table
        name="users"
        class="mt-12"
        :items="data.data"
        item-name="user"
        :headers="headers"
        :loading="pending"
        :heading="$t('users', 2)"
        :filters="{ enabled: true }"
        :pagination="data.meta"
        :server-action="serverAction"
        :action-create="actionCreate"
        :actions-validations="actionsValidations"
        dialog-form-component-path="admin/forms/User"
        @paginating="onPaginate"
        @item-created="onItemCreated"
        @item-updated="onItemUpdated"
        @item-deleted="onItemDeleted">
        <template #[`filters.items`]>
            <v-list-item class="pb-0">
                <v-select
                    v-model="status"
                    item-value="key"
                    density="compact"
                    :items="statuses"
                    variant="underlined"
                    :disabled="pending"
                    :label="$t('user_status')"
                    :clearable="Boolean(status)"
                    @update:model-value="updateQueryString('status')" />
            </v-list-item>
        </template>

        <template #[`item.role`]="{ item: user }">
            <span v-text="pluck(user.roles, 'name', true) || 'N/A'" />
        </template>

        <template #[`item.active`]="{ item: user }">
            <span v-text="$t(user.active ? 'active' : 'banned')" />
        </template>

        <template #append-actions="{ item: user }">
            <v-tooltip v-if="!userIsAdmin(user) && $can('users.toggle')" location="top">
                <template #activator="{ props }">
                    <v-icon
                        size="small"
                        :disabled="pending"
                        :color="user.active ? 'error' : 'success'"
                        :icon="`mdi-${user.active ? 'cancel' : 'check'}`"
                        v-bind="props"
                        @click="toggleStatus(user)" />
                </template>
                <span v-text="$t('btns.toggle_user_account', { active: user.active })" />
            </v-tooltip>
        </template>
    </app-data-table>
</template>

<script setup>
    import useHelpers from '~/composables/helpers'
    import useDataTable from '~/composables/data-table'

    const { t } = useI18n()
    const { $can, pluck } = useHelpers()
    const { $swal, $axios } = useNuxtApp()
    const route = useRoute()

    useHead({
        title: t('users', 2),
    })

    const serverAction = ref('/admin/users')
    const {
        setData,
        onPaginate,
        updateQueryString,
        onItemCreated,
        onItemUpdated,
        onItemDeleted,
    } = useDataTable(serverAction)

    const { data, pending } = await useAsyncData('users', () => $axios.$get(serverAction.value, { params: {
        s: route.query.s,
        page: route.query.page,
        status: route.query.status,
    }}), { watch: [
        () => route.query.s,
        () => route.query.page,
        () => route.query.status,
    ]})

    const status = ref(route.query.status ?? null)
    const actionsValidations = ref({
        update: {
            callback: user => !userIsAdmin(user),
        },
        delete: {
            callback: user => !userIsAdmin(user),
        },
    })

    const headers = computed(() => [
        { title: t('id'), key: 'id', sortable: true },
        { title: t('name'), key: 'name', sortable: true },
        { title: t('role'), key: 'role', sortable: true },
        { title: t('email'), key: 'email', sortable: true },
        { title: t('status'), key: 'active', sortable: true },
    ])

    const actionCreate = computed(() => ({
        icon: 'mdi-account-plus', text: t('create', [t('users')]),
    }))

    const statuses = computed(() => [
        { title: `-- ${t('labels.all')} --`, key: null },
        { title: t('active'), key: '1' },
        { title: t('banned'), key: '0' },
    ])

    async function toggleStatus (item) {
        const text = t(item.active
            ? 'alerts.ban_user'
            : 'alerts.unban_user'
        )

        const res = await $swal.confirm({
            text,
            method: 'patch',
            url: `/admin/users/${item.id}/toggle`,
        })

        if (!res.dismiss) {
            onItemUpdated({ item, data: res.data })
        }
    }

    function userIsAdmin (user) {
        return user.roles.some(role => role.name === 'Super Admin')
    }

    watch(data, (value) => {
        setData(value)
    }, { immediate: true })
</script>
