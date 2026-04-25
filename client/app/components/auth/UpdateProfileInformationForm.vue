<template>
    <app-form
        :form="form"
        hide-heading
        hide-divider
        :action="action"
        :disabled="form.busy"
        :container-options="formContainerOptions"
        v-on="$attrs"
        @success="onFormSuccess">
        <v-text-field
            v-model="form.name"
            autofocus
            hide-details
            class="my-2"
            variant="underlined"
            density="comfortable"
            prepend-icon="mdi-account"
            :label="$t('labels.name')"
            :error="form.errors.has('name')"
            :error-messages="form.errors.get('name')" />

        <v-text-field
            v-model="form.email"
            type="email"
            hide-details
            variant="underlined"
            density="comfortable"
            prepend-icon="mdi-email"
            :label="$t('labels.email')"
            :error="form.errors.has('email')"
            :error-messages="form.errors.get('email')" />

        <template #actions="{ handleCancelClick }">
            <div class="d-flex mr-2">
                <v-spacer />
                <v-btn
                    class="mr-2"
                    color="error"
                    size="x-small"
                    icon="mdi-close"
                    :disabled="form.busy"
                    @click="handleCancelClick" />
                <v-btn
                    class="mr-2"
                    type="submit"
                    size="x-small"
                    color="primary"
                    icon="mdi-check"
                    :loading="form.busy" />
            </div>
        </template>
    </app-form>
</template>

<script setup>
    import useForm from '~/composables/form'
    import useHelpers from '~/composables/helpers'
    import { useAuthStore } from '~/store/auth'

    const authStore = useAuthStore()
    const { t } = useI18n()
    const { $auth } = useNuxtApp()
    const { $notify } = useHelpers()
    const { form, clearForm } = useForm({
        name: '',
        email: '',
    }, authStore.user)

    const action = form => $auth.updateProfileInformation(form)
    const formContainerOptions = { class: 'mb-2', elevation: 0 }

    function onFormSuccess () {
        clearForm()
        $notify(t('alerts.updated'))
    }
</script>
