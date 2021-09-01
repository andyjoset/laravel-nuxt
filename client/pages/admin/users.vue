<template>
    <v-card class="mt-12">
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
                    <div class="text-h5 pl-3" v-text="'Users'" />
                </v-card-title>

                <v-toolbar flat>
                    <v-tooltip v-if="$can('users.store')" top>
                        <template #activator="{ on }">
                            <v-btn
                                small
                                color="primary"
                                :disabled="fetching"
                                @click="dialog = true"
                                v-on="on">
                                <v-icon small>mdi-account-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Create New User</span>
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

                    <v-dialog v-model="dialog" max-width="500px" persistent>
                        <user-form :user="item" :readonly="readonly" @close="clear" />
                    </v-dialog>
                </v-toolbar>
            </template>

            <template #[`item.status`]="{ item: user }">
                <span v-text="user.active ? 'Active' : 'Banned'" />
            </template>

            <template #[`item.actions`]="{ item: user }">
                <v-tooltip top>
                    <template #activator="{ on }">
                        <v-icon
                            small
                            color="blue"
                            :disabled="fetching"
                            @click="openDialog(user, true)"
                            v-on="on"
                            v-text="'mdi-eye'" />
                    </template>
                    <span>Show</span>
                </v-tooltip>

                <v-tooltip v-if="$can('users.update')" top>
                    <template #activator="{ on }">
                        <v-icon
                            small
                            color="success"
                            :disabled="fetching"
                            @click="openDialog(user)"
                            v-on="on"
                            v-text="'mdi-pencil'" />
                    </template>
                    <span>Edit</span>
                </v-tooltip>

                <v-tooltip v-if="$can('users.delete')" top>
                    <template #activator="{ on }">
                        <v-icon
                            small
                            color="error"
                            :disabled="fetching"
                            @click="deleteItem(user)"
                            v-on="on"
                            v-text="'mdi-delete'" />
                    </template>
                    <span>Delete</span>
                </v-tooltip>

                <v-tooltip v-if="$can('users.toggle')" top>
                    <template #activator="{ on }">
                        <v-icon
                            small
                            :color="user.active ? 'error' : 'success'"
                            :disabled="fetching"
                            @click="toggleStatus(user)"
                            v-on="on">
                            mdi-{{ user.active ? 'cancel' : 'check' }}
                        </v-icon>
                    </template>
                    <span>{{ user.active ? 'Ban' : 'Activate' }} user account</span>
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
    import UserForm from '~/components/admin/forms/User'

    export default {
        components: {
            UserForm,
        },

        async asyncData ({ $axios, query }) {
            try {
                const { data: items, meta: pagination } = await $axios.$get('/admin/users', { params: query })

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
                { text: 'Email', value: 'email', sortable: true },
                { text: 'Status', value: 'status', sortable: true },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
        }),

        head: () => ({
            title: 'Users',
        }),

        watchQuery: ['page', 's'],

        methods: {
            openDialog (item, readonly = false) {
                this.item = item
                this.dialog = true
                this.readonly = readonly
            },
            async deleteItem (item) {
                const index = this.items.findIndex(user => user.id === item.id)
                const { dismiss } = await this.$swalDelete({ url: `/admin/users/${item.id}` })

                if (!dismiss) {
                    this.$notify('Deleted Successfully!')
                    this.items.splice(index, 1)
                }
            },
            async toggleStatus (item) {
                const text = item.active
                    ? 'The user won\'t be able to log into the application until the account is activated again.'
                    : 'The user will be able to log into the application before this.'

                const res = await this.$swalConfirm({
                    text,
                    method: 'patch',
                    url: `/admin/users/${item.id}/toggle`,
                })

                if (!res.dismiss) {
                    Object.assign(item, res.data)
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
            },
        },
    }
</script>
