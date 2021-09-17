<template>
    <v-form :readonly="readonly" :disabled="form.busy" @submit.prevent="submit">
        <v-card>
            <v-card-title class="my-0">
                <v-sheet
                    rounded
                    width="100%"
                    elevation="6"
                    max-width="100%"
                    color="primary"
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

                <v-text-field
                    v-model="form.email"
                    dense
                    class="mb-4"
                    type="email"
                    prepend-icon="mdi-email"
                    :label="$t('labels.email')"
                    :error="form.errors.has('email')"
                    :error-messages="form.errors.get('email')" />

                <app-select
                    v-if="$can('users.assign-role')"
                    v-model="form.role_id"
                    dense
                    item-text="name"
                    item-value="id"
                    server-action="/roles"
                    prepend-icon="mdi-shield"
                    :label="$t('labels.role')"
                    :error="form.errors.has('role_id')"
                    :error-messages="form.errors.get('role_id')" />
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
            user: {
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
            fetching: false,
            form: vm.$vform.make({
                name: '',
                email: '',
                role_id: null,
            }),
        }),

        computed: {
            title () {
                if (this.readonly) {
                    return this.$t('user_info')
                }

                return this.$t(this.user ? 'edit' : 'create', [this.$tc('users')])
            },
            url () {
                return this.user ? `/admin/users/${this.user.id}` : '/admin/users'
            },
            method () {
                return this.user ? 'put' : 'post'
            },
            userIsSuperAdmin () {
                return this.user?.roles?.includes(role => role.name === 'Super Admin')
            },
        },

        watch: {
            user () {
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
                        this.$t('alerts.' + (this.user ? 'updated' : 'created'))
                    )

                    this.close(data)
                } catch (e) {}
            },
            close (data = null) {
                this.$emit('close', data)
            },
            fillForm () {
                this.form.clear()
                this.form.reset()

                if (this.user) {
                    this.form.fill({
                        ...this.user,
                        role_id: this.user.roles[0]?.id || null,
                    })
                }
            },
        }
    }
</script>
