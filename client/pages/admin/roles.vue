<template>
    <v-card class="mt-4">
        <v-data-table
            :items="items"
            :search="search"
            :headers="headers"
            :loader-height="2"
            :loading="fetching"
            hide-default-footer
            class="elevation-2 px-2">
            <template #top>
                <v-card-title class="align-start mb-2">
                    <v-sheet
                        rounded
                        elevation="6"
                        color="primary"
                        max-width="100%"
                        class="mt-n9">
                        <div class="pa-5">
                            <v-icon class="white--text">mdi-format-list-bulleted</v-icon>
                        </div>
                    </v-sheet>
                    <div class="text-h5 pl-3" v-text="'Roles'" />
                </v-card-title>

                <v-toolbar flat>
                    <v-tooltip v-if="$can('roles.store')" top>
                        <template #activator="{ on }">
                            <v-btn small color="primary" @click="dialog = true" v-on="on">
                                <v-icon small>mdi-plus-box-multiple</v-icon>
                            </v-btn>
                        </template>
                        <span>Create New Role</span>
                    </v-tooltip>

                    <v-divider class="mx-4" inset vertical />
                    <v-spacer />

                    <v-text-field
                        v-model="search"
                        single-line
                        label="Search"
                        :disabled="fetching"
                        append-icon="mdi-magnify"
                        :hide-details="!search"
                        :persistent-hint="Boolean(search)"
                        hint="Press enter for a deeper search"
                        @keydown.enter="$refs.pagination.paginate()" />

                    <v-dialog v-model="dialog" max-width="700px" persistent>
                        <role-form :role="item" :readonly="readonly" @close="clear" />
                    </v-dialog>
                </v-toolbar>
            </template>

            <template #[`item.permissions`]="{ item: role }">
                <span v-text="pluck(role.permissions, 'description', true) || 'N/A'" />
            </template>

            <template #[`item.actions`]="{ item: role }">
                <v-tooltip top>
                    <template #activator="{ on }">
                        <v-icon
                            small
                            color="blue"
                            @click="openDialog(role, true)"
                            v-on="on"
                            v-text="'mdi-eye'" />
                    </template>
                    <span>Show</span>
                </v-tooltip>

                <v-tooltip v-if="role.name !== 'Super Admin' && $can('roles.update')" top>
                    <template #activator="{ on }">
                        <v-icon
                            small
                            color="success"
                            @click="openDialog(role)"
                            v-on="on"
                            v-text="'mdi-pencil'" />
                    </template>
                    <span>Edit</span>
                </v-tooltip>

                <v-tooltip v-if="role.name !== 'Super Admin' && $can('roles.delete')" top>
                    <template #activator="{ on }">
                        <v-icon
                            small
                            color="error"
                            @click="deleteItem(role)"
                            v-on="on"
                            v-text="'mdi-delete'" />
                    </template>
                    <span>Delete</span>
                </v-tooltip>
            </template>

            <template #footer>
                <v-divider />
                <pagination
                    v-show="pagination.last_page > 1"
                    ref="pagination"
                    v-model="pagination.current_page"
                    :config="pagination"
                    :disabled="fetching"
                    :append-query="{ s: search }"
                    @paginating="fetching = $event.status === 'start'" />
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
    import RoleForm from '~/components/admin/forms/Role'

    export default {
        components: {
            RoleForm,
        },

        async asyncData ({ $axios, query }) {
            try {
                const { data: items, meta: pagination } = await $axios.$get('/admin/roles', { params: query })

                return { items, pagination, fetching: false }
            } catch (e) {
            }

            return { fetching: false }
        },

        data: () => ({
            items: [],
            pagination: {},
            item: null,
            dialog: false,
            readonly: false,
            fetching: true,
            search: '',
            headers: [
                { text: 'ID', value: 'id', sortable: true },
                { text: 'Name', value: 'name', sortable: true },
                { text: 'Permissions', value: 'permissions', sortable: true },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
        }),

        head: () => ({
            title: 'Roles',
        }),

        watchQuery: ['page', 's'],

        methods: {
            openDialog (item, readonly = false) {
                this.item = item
                this.dialog = true
                this.readonly = readonly
            },
            async deleteItem (item) {
                const index = this.items.findIndex(role => role.id === item.id)
                const { dismiss } = await this.$swalDelete({ url: `/admin/roles/${item.id}` })

                if (!dismiss) {
                    this.$notify('Deleted Successfully!')
                    this.items.splice(index, 1)
                }
            },
            clear (data = null) {
                this.dialog = false
                this.item = null
                this.readonly = false

                if (data) {
                    const item = this.items.find(item => item.id === data.id)

                    item ? Object.assign(item, data) : this.items.push(data)
                }
            }
        },
    }
</script>
