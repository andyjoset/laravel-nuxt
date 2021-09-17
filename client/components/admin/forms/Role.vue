<template>
    <v-form :readonly="readonly" :disabled="form.busy" @submit.prevent="submit">
        <v-card>
            <v-card-title class="my-0">
                <v-sheet
                    rounded
                    width="100%"
                    elevation="6"
                    color="primary"
                    max-width="100%"
                    class="text-center">
                    <v-theme-provider>
                        <div class="pa-7">
                            <span class="text-h5">
                                <v-icon>mdi-clipboard-text</v-icon> {{ title }}
                            </span>
                        </div>
                    </v-theme-provider>
                </v-sheet>
            </v-card-title>

            <v-card-text>
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
            </v-card-text>

            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="error"
                    :disabled="form.busy"
                    @click="close()">
                    <v-icon class="mr-1">mdi-close-circle</v-icon> {{ $t('btns.cancel') }}
                </v-btn>
                <v-btn
                    v-if="!readonly"
                    type="submit"
                    color="primary"
                    :loading="form.busy">
                    <v-icon class="mr-1">mdi-check-circle</v-icon> {{ $t('btns.save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-form>
</template>

<script>
    export default {
        props: {
            role: {
                type: Object,
                required: false,
                default: () => null,
            },
            readonly: {
                type: Boolean,
                required: false,
                default: () => false,
            },
        },

        data: vm => ({
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
            url () {
                return this.role ? `/admin/roles/${this.role.id}` : '/admin/roles'
            },
            method () {
                return this.role ? 'put' : 'post'
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

        watch: {
            role () {
                this.fillForm()
            }
        },

        created () {
            this.fillForm()
        },

        methods: {
            async submit () {
                try {
                    const { data } = await this.form[this.method](this.url)

                    this.$notify(
                        this.$t('alerts.' + (this.role ? 'updated' : 'created'))
                    )

                    this.close(data)
                } catch (e) {}
            },
            close (data = null) {
                this.$emit('close', data)

                this.$nextTick(() => {
                    this.form.clear()
                    this.form.reset()
                })
            },
            fillForm () {
                this.form.clear()
                this.form.reset()

                if (this.role) {
                    this.form.fill(this.role)
                    this.form.permissions = this.pluck(this.role.permissions, 'id')
                }
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
