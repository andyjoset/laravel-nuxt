<template>
    <app-data-table
        name="users"
        class="mt-12"
        :items="items"
        item-name="user"
        :headers="headers"
        :loading="fetching"
        :heading="$tc('users', 2)"
        :filters="{ enabled: true }"
        :pagination="pagination"
        :server-action="serverAction"
        :action-create="actionCreate"
        :actions-validations="actionsValidations"
        dialog-form-component-path="admin/forms/User"
        @paginating="onPaginate"
        @item-created="onItemCreated"
        @item-updated="onItemUpdated"
        @item-deleted="onItemDeleted">
        <template #[`filters.items`]>
            <v-list-item>
                <v-list-item-content class="pb-0">
                    <v-select
                        v-model="status"
                        dense
                        outlined
                        :items="statuses"
                        :disabled="fetching"
                        :label="$t('user_status')"
                        :clearable="Boolean(status)"
                        @change="updateQueryString('status')" />
                </v-list-item-content>
            </v-list-item>
        </template>

        <template #[`item.active`]="{ item: user }">
            <span v-t="user.active ? 'active' : 'banned'" />
        </template>

        <template #append-actions="{ item: user }">
            <v-tooltip v-if="!userIsAdmin(user) && $can('users.toggle')" top>
                <template #activator="{ on }">
                    <v-icon
                        small
                        :disabled="fetching"
                        :color="user.active ? 'error' : 'success'"
                        @click="toggleStatus(user)"
                        v-on="on">
                        mdi-{{ user.active ? 'cancel' : 'check' }}
                    </v-icon>
                </template>
                <span v-t="{ path: 'btns.toggle_user_account', args: { active: user.active } }" />
            </v-tooltip>
        </template>
    </app-data-table>
</template>

<script>
    import InteractsWithDataTable from '~/components/mixins/InteractsWithDataTable'

    export default {
        mixins: [InteractsWithDataTable],

        async asyncData ({ $axios, query }) {
            try {
                const { data: items, meta: pagination } = await $axios.$get('/admin/users', { params: query })

                return {
                    items,
                    pagination,
                    fetching: false,
                    status: query.status ?? null,
                }
            } catch (e) {
            }

            return { fetching: false }
        },

        data: vm => ({
            status: null,
            serverAction: '/admin/users',
            actionsValidations: {
                update: {
                    callback: user => !vm.userIsAdmin(user),
                },
                delete: {
                    callback: user => !vm.userIsAdmin(user),
                },
            },
        }),

        head: vm => ({
            title: vm.$tc('users', 2),
        }),

        computed: {
            headers () {
                return [
                    { text: this.$t('id'), value: 'id', sortable: true },
                    { text: this.$tc('name'), value: 'name', sortable: true },
                    { text: this.$t('email'), value: 'email', sortable: true },
                    { text: this.$t('status'), value: 'active', sortable: true },
                ]
            },
            actionCreate () {
                return { icon: 'mdi-account-plus', text: this.$t('create', [this.$tc('users')]) }
            },
            statuses () {
                return [
                    { text: `-- ${this.$t('labels.all')} --`, value: null },
                    { text: this.$t('active'), value: '1' },
                    { text: this.$t('banned'), value: '0' },
                ]
            },
        },

        watchQuery: [...InteractsWithDataTable.watchQuery, 'status'],

        methods: {
            async toggleStatus (item) {
                const text = this.$t(item.active
                    ? 'alerts.ban_user'
                    : 'alerts.unban_user'
                )

                const res = await this.$swal.confirm({
                    text,
                    method: 'patch',
                    url: `/admin/users/${item.id}/toggle`,
                })

                if (!res.dismiss) {
                    this.onItemUpdated({ item, data: res.data })
                }
            },
            userIsAdmin (user) {
                return user.roles.some(role => role.name === 'Super Admin')
            },
        },
    }
</script>
