<template>
    <app-data-table
        name="roles"
        class="mt-12"
        :items="items"
        item-name="role"
        :headers="headers"
        :loading="fetching"
        :heading="$tc('roles', 2)"
        :pagination="pagination"
        :server-action="serverAction"
        :button-create="buttonCreate"
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

<script>
    import InteractsWithDataTable from '~/components/mixins/InteractsWithDataTable'

    export default {
        mixins: [InteractsWithDataTable],

        async asyncData ({ $axios, query }) {
            try {
                const { data: items, meta: pagination } = await $axios.$get('/admin/roles', { params: query })

                return { items, pagination, fetching: false }
            } catch (e) {
            }

            return { fetching: false }
        },

        data: () => ({
            serverAction: '/admin/roles',
            actionsValidations: {
                update: {
                    callback: role => role.name !== 'Super Admin',
                },
                delete: {
                    callback: role => role.name !== 'Super Admin',
                },
            },
        }),

        head: vm => ({
            title: vm.$tc('roles', 2),
        }),

        computed: {
            headers () {
                return [
                    { text: this.$t('id'), value: 'id', sortable: true },
                    { text: this.$tc('name'), value: 'name', sortable: true },
                    { text: this.$tc('permissions', 2), value: 'permissions', sortable: true },
                ]
            },
            buttonCreate () {
                return { text: this.$t('create', [this.$tc('roles')]) }
            },
        },
    }
</script>
