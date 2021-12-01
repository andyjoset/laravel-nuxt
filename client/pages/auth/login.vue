<template>
    <app-form
        :form="form"
        :action="action"
        :disabled="form.busy"
        :title="$t('sign_in')"
        icon="mdi-fingerprint"
        :heading-options="formHeadingOptions"
        :container-options="formContainerOptions">
        <v-alert v-if="form.errors.any()" type="error" dense dismissible>
            {{ form.errors.first() }}
        </v-alert>

        <v-text-field
            v-model="form.email"
            autofocus
            type="email"
            prepend-icon="mdi-email"
            :label="$t('labels.email')" />

        <v-text-field
            v-model="form.password"
            class="mb-4"
            type="password"
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
                v-t="'forgot_password'"
                text
                rounded
                x-small
                class="pt-1"
                color="primary"
                :disabled="form.busy"
                @click="dialogReset = true" />
        </div>

        <template #actions>
            <v-card-actions class="text-caption">
                <v-col cols="12" class="text-center">
                    <v-btn
                        type="submit"
                        dark
                        block
                        color="primary"
                        :loading="form.busy">
                        {{ $t('btns.submit') }}
                    </v-btn>

                    <br>
                    <i18n path="no_account.text">
                        <template #action>
                            <v-btn
                                text
                                rounded
                                x-small
                                color="primary"
                                :disabled="form.busy"
                                :to="{ name: 'register' }">
                                {{ $t('no_account.action_text') }}
                            </v-btn>
                        </template>
                    </i18n>
                </v-col>
            </v-card-actions>
        </template>

        <v-dialog v-model="dialogReset" max-width="500" persistent>
            <password-reset-request
                @cancel="dialogReset = false"
                @success="dialogReset = false" />
        </v-dialog>
    </app-form>
</template>

<script>
    import HasForm from '~/components/mixins/HasForm'
    import PasswordResetRequest from '~/components/auth/PasswordResetRequest'

    export default {
        components: {
            PasswordResetRequest,
        },

        mixins: [HasForm],

        middleware: ['guest'],

        data: vm => ({
            dialogReset: null,
            action: form => vm.$auth.login(form),
            formHeadingOptions: {
                class: 'mt-n10'
            },
            formContainerOptions: {
                elevation: 12,
                maxWidth: 450,
                class: 'mx-auto my-12',
            },
            form: vm.$vform.make({
                email: '',
                password: '',
                remember: null,
            }),
        }),

        head: vm => ({
            title: vm.$t('login'),
        }),
    }
</script>
