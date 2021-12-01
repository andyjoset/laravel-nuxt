<template>
    <app-form
        :form="form"
        :action="action"
        :disabled="form.busy"
        icon="mdi-shield-account"
        :title="$t('change_password')"
        :container-options="formContainerOptions"
        v-on="$listeners"
        @cancel="clearForm"
        @success="onFormSuccess">
        <v-text-field
            v-model="form.current_password"
            autofocus
            class="my-4"
            type="password"
            prepend-icon="mdi-lock"
            :label="$t('labels.current_password')"
            :error="form.errors.has('current_password')"
            :error-messages="form.errors.get('current_password')" />

        <v-text-field
            v-model="form.password"
            class="my-4"
            type="password"
            prepend-icon="mdi-lock"
            :label="$t('labels.password_confirmation')"
            :error="form.errors.has('password')"
            :error-messages="form.errors.get('password')" />

        <v-text-field
            v-model="form.password_confirmation"
            class="my-4"
            type="password"
            prepend-icon="mdi-lock"
            :label="$t('labels.new_password')"
            :error="form.errors.has('password')"
            :error-messages="form.errors.get('password')" />
    </app-form>
</template>

<script>
    import HasForm from '~/components/mixins/HasForm'

    export default {
        mixins: [HasForm],

        data: vm => ({
            action: form => vm.$auth.updatePassword(form),
            formContainerOptions: { class: 'pa-2' },
            form: vm.$vform.make({
                password: '',
                current_password: '',
                password_confirmation: '',
            })
        }),

        methods: {
            onFormSuccess () {
                this.clearForm()

                this.$notify(this.$t('password_updated'))
            },
        }
    }
</script>
