<template>
    <app-form
        :form="form"
        :title="title"
        :action="action"
        :method="method"
        :readonly="readonly"
        :disabled="form.busy || $fetchState.pending"
        v-on="$listeners"
        @success="onFormSuccess"
        @cancel="onFormCancelled">
        <v-text-field
            v-model="form.name"
            dense
            autofocus
            class="my-4"
            prepend-icon="mdi-face"
            :label="$tc('labels.name')"
            :error="form.errors.has('name')"
            :error-messages="form.errors.get('name')" />

        <v-row v-if="showPermissions">
            <v-col cols="12" class="pb-0 mb-0">
                <strong :class="form.errors.has('permissions') ? 'red--text' : ''">
                    {{ $tc('permissions', 2) }}:
                </strong>
                <p class="red--text" v-text="form.errors.get('permissions')" />
            </v-col>

            <v-col v-if="$fetchState.pending" cols="4" offset="4" class="text-center">
                <v-progress-circular indeterminate size="26" width="3" color="primary" />
            </v-col>

            <v-col v-for="(option, i) in Object.keys(permissions)" :key="i" sm="12" md="6" lg="6" xl="3">
                <v-treeview
                    dense
                    shaped
                    open-all
                    hoverable
                    transition
                    :selectable="!readonly"
                    :items="[permissions[option]]"
                    :value="getTreeSelection(option)"
                    @input="updateSelection($event, option)" />
            </v-col>
        </v-row>
    </app-form>
</template>

<script>
    import HasForm from '~/components/mixins/HasForm'

    export default {
        mixins: [HasForm],

        props: {
            role: {
                type: Object,
                required: false,
                default: () => null,
            },
        },

        data: vm => ({
            formInitialValuesProp: 'role',
            form: vm.$vform.make({
                name: '',
                permissions: [],
            }),
        }),

        async fetch () {
            if (!this.$store.getters['commons/permissions'].length) {
                await this.$store.dispatch('commons/fetchPermissions')
            }
        },

        computed: {
            title () {
                if (this.readonly) {
                    return this.$t('role_info')
                }

                return this.$t(this.role ? 'edit' : 'create', [this.$tc('roles')])
            },
            action () {
                return this.role ? `/admin/roles/${this.role.id}` : '/admin/roles'
            },
            showPermissions () {
                return this.role?.name !== 'Super Admin'
            },
            permissions () {
                const items = {}
                const permissions = this.$store.getters['commons/permissions']

                for (const option in this.groupByKey(permissions, 'module')) {
                    const children = permissions.filter((permission) => {
                        const result = permission.module === option

                        if (this.readonly) {
                            return result && this.pluck(this.role.permissions, 'id').includes(permission.id)
                        }

                        return result
                    })

                    if (this.readonly && !children.length) {
                        children.push({ id: 'N/A', description: 'N/A' })
                    }

                    items[option] = {
                        id: permissions.indexOf(option),
                        name: option,
                        children: children.map(p => Object({ id: p.id, name: p.description }))
                    }
                }

                return items
            },
        },

        methods: {
            getFormValues () {
                const values = { ...this.role, permissions: [] }

                if (this.role) {
                    values.permissions = this.pluck(this.role.permissions, 'id')
                }

                return values
            },
            updateSelection (values, scope) {
                const ids = this.pluck(this.permissions[scope].children, 'id')
                const result = this.form.permissions.filter(id => !ids.includes(id))

                result.push(...values)

                this.form.permissions = result
            },
            getTreeSelection (scope) {
                const ids = this.pluck(this.permissions[scope].children, 'id')
                const result = this.form.permissions.filter(id => ids.includes(id))

                return result
            },
        }
    }
</script>
