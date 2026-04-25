<template>
    <app-form
        :form="form"
        :action="action"
        :disabled="form.busy"
        :title="$t('sign_in')"
        autocomplete="off"
        icon="mdi-fingerprint"
        :heading-options="formHeadingOptions"
        :container-options="formContainerOptions">
        <v-alert
            v-if="form.errors.any()"
            closable
            type="error"
            class="my-3"
            icon="mdi-alert"
            density="compact"
            :text="form.errors.first()" />

        <v-text-field
            v-model="form.email"
            autofocus
            class="mt-4"
            type="email"
            variant="underlined"
            prepend-icon="mdi-email"
            :label="$t('labels.email')" />

        <v-text-field
            v-model="form.password"
            class="mb-2"
            type="password"
            variant="underlined"
            prepend-icon="mdi-lock"
            :label="$t('labels.password')" />

        <div class="d-flex justify-space-between">
            <v-checkbox
                v-model="form.remember"
                class="pa-0 ma-0"
                :true-value="true"
                :false-value="null"
                :label="$t('labels.remember')" />

            <v-btn
                rounded
                class="mt-4"
                size="x-small"
                variant="text"
                color="primary"
                :disabled="form.busy"
                @click="dialogReset = true">
                {{ $t('forgot_password') }}
            </v-btn>
        </div>

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
                        {{ $t('btns.submit') }}
                    </v-btn>

                    <br>
                    <span v-text="$t('no_account.text')" />
                    <v-btn
                        rounded
                        class="mx-0"
                        size="x-small"
                        variant="text"
                        color="primary"
                        :disabled="form.busy"
                        :to="{ name: 'register' }">
                        {{ $t('no_account.action_text') }}
                    </v-btn>
                </v-col>
            </v-card-actions>
        </template>

        <ClientOnly>
            <v-dialog v-model="dialogReset" max-width="500" persistent>
                <password-reset-request
                    @cancel="dialogReset = false"
                    @success="dialogReset = false" />
            </v-dialog>
        </ClientOnly>
    </app-form>
</template>

<script setup>
    import useForm from '~/composables/form'
    import PasswordResetRequest from '~/components/auth/PasswordResetRequest'

    definePageMeta({
        middleware: ['guest'],
    })

    const { t } = useI18n()
    const { $auth } = useNuxtApp()
    const { form } = useForm({
        email: '',
        password: '',
        remember: null,
    })

    useHead({
        title: t('login'),
    })

    const dialogReset = ref(null)
    const action = form => $auth.login(form)
    const formHeadingOptions = {
        class: 'mt-n10',
    }

    const formContainerOptions = {
        elevation: 12,
        maxWidth: 450,
        class: 'mx-auto my-12',
    }
</script>
