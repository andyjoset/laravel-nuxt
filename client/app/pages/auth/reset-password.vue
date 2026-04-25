<template>
    <app-form
        :form="form"
        :action="action"
        :disabled="form.busy"
        icon="mdi-lock-open"
        :title="$t('reset_password')"
        :heading-options="formHeadingOptions"
        :container-options="formContainerOptions"
        @success="onFormSuccess">
        <v-alert v-if="form.errors.any()" type="error" icon="mdi-alert" density="compact" closable class="my-3">
            {{ form.errors.first() }}
        </v-alert>

        <v-text-field
            v-model="form.email"
            autofocus
            class="my-4"
            type="email"
            variant="underlined"
            prepend-icon="mdi-email"
            :label="$t('labels.email')" />

        <v-text-field
            v-model="form.password"
            type="password"
            variant="underlined"
            prepend-icon="mdi-lock"
            :label="$t('labels.password')" />

        <v-text-field
            v-model="form.password_confirmation"
            type="password"
            variant="underlined"
            prepend-icon="mdi-lock"
            :label="$t('labels.password_confirmation')" />

        <template #actions>
            <v-card-actions class="text-caption">
                <v-col cols="12" class="text-center">
                    <v-btn
                        type="submit"
                        dark
                        block
                        color="primary"
                        variant="elevated"
                        :loading="form.busy">
                        {{ $t('reset') }}
                    </v-btn>
                </v-col>
            </v-card-actions>
        </template>
    </app-form>
</template>

<script setup>
    import useForm from '~/composables/form'
    import useHelpers from '~/composables/helpers'

    const { t } = useI18n()
    const { $auth } = useNuxtApp()
    const { $notify } = useHelpers()
    const route = useRoute()
    const router = useRouter()
    const { form, clearForm } = useForm({
        token: '',
        email: '',
        password: '',
        password_confirmation: '',
    })

    const action = form => $auth.forgotPassword(form, 'reset')
    const formHeadingOptions = {
        class: 'mt-n10'
    }

    const formContainerOptions = {
        elevation: 12,
        maxWidth: 450,
        class: 'mx-auto my-12',
    }

    useHead({
        title: t('reset_password'),
    })

    onMounted (() => {
        form.update({
            email: route.query.email,
            token: route.params.token,
        })
    })

    function onFormSuccess (data) {
        clearForm()

        $notify(data.message)

        router.replace({ name: 'login' })
    }
</script>
