<template>
    <app-form
        :form="form"
        :title="title"
        :action="action"
        :method="method"
        :readonly="readonly"
        :disabled="form.busy"
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
    </app-form>
</template>

<script>
    import HasForm from '~/components/mixins/HasForm'

    export default {
        mixins: [HasForm],

        props: {
            user: {
                type: Object,
                required: false,
                default: () => null,
            },
        },

        data: vm => ({
            formInitialValuesProp: 'user',
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
            action () {
                return this.user ? `/admin/users/${this.user.id}` : '/admin/users'
            },
            userIsSuperAdmin () {
                return this.user?.roles?.includes(role => role.name === 'Super Admin')
            },
        },

        methods: {
            getFormValues () {
                const values = { ...this.user }

                if (this.user) {
                    values.role_id = this.user.roles[0]?.id || null
                }

                return values
            },
        }
    }
</script>
