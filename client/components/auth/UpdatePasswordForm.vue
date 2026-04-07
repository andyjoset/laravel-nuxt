<template>
    <app-form
        :form="form"
        :action="action"
        :disabled="form.busy"
        icon="mdi-shield-account"
        :title="$t('change_password')"
        :container-options="formContainerOptions"
        v-on="$attrs"
        @cancel="clearForm"
        @success="onFormSuccess">
        <v-text-field
            v-model="form.current_password"
            autofocus
            class="my-4"
            type="password"
            variant="underlined"
            prepend-icon="mdi-lock"
            :label="$t('labels.current_password')"
            :error="form.errors.has('current_password')"
            :error-messages="form.errors.get('current_password')" />

        <v-text-field
            v-model="form.password"
            class="my-4"
            type="password"
            variant="underlined"
            prepend-icon="mdi-lock"
            :label="$t('labels.password_confirmation')"
            :error="form.errors.has('password')"
            :error-messages="form.errors.get('password')" />

        <v-text-field
            v-model="form.password_confirmation"
            class="my-4"
            type="password"
            variant="underlined"
            prepend-icon="mdi-lock"
            :label="$t('labels.new_password')"
            :error="form.errors.has('password')"
            :error-messages="form.errors.get('password')" />
    </app-form>
</template>

<script setup>
    import useForm from '~/composables/form'
    import useHelpers from '~/composables/helpers'

    const { t } = useI18n()
    const { $auth } = useNuxtApp()
    const { $notify } = useHelpers()
    const { form, clearForm } = useForm({
        password: '',
        current_password: '',
        password_confirmation: '',
    })

    const action = form => $auth.updatePassword(form)
    const formContainerOptions = { class: 'pa-2' }

    function onFormSuccess () {
        clearForm()
        $notify(t('password_updated'))
    }
</script>
