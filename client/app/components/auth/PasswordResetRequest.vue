<template>
    <app-form
        :form="form"
        :action="action"
        icon="mdi-lock-open"
        :disabled="form.busy"
        :title="$t('request_password_reset')"
        :floating-heading="false"
        v-on="$attrs"
        @success="onFormSuccess">
        <v-alert
            v-if="form.errors.any()"
            closable
            type="error"
            icon="mdi-alert"
            density="compact"
            :text="form.errors.first()" />

        <v-text-field
            v-model="form.email"
            autofocus
            class="my-4"
            type="email"
            variant="underlined"
            prepend-icon="mdi-email"
            :label="$t('labels.email')" />

        <template #[`actions.save`]>
            <v-btn
                type="submit"
                color="primary"
                :loading="form.busy">
                <v-icon class="mr-1" icon="mdi-check-circle" /> {{ $t('btns.submit') }}
            </v-btn>
        </template>
    </app-form>
</template>

<script setup>
    import useForm from '~/composables/form'
    import useHelpers from '~/composables/helpers'

    const { $notify } = useHelpers()
    const { $auth } = useNuxtApp()
    const { clearForm, form } = useForm({
        email: '',
    })

    const action = form => $auth.forgotPassword(form, 'forgot')

    function onFormSuccess (data) {
        clearForm()
        $notify(data.message)
    }
</script>
