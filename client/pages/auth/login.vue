<template>
    <v-form autocomplete="off" :disabled="form.busy" @submit.prevent="$auth.login(form)">
        <v-card
            elevation="12"
            max-width="450"
            class="mx-auto my-12">
            <v-card-title class="pb-2">
                <v-sheet
                    rounded
                    width="100%"
                    elevation="6"
                    max-width="420"
                    color="primary"
                    class="overflow-hidden text-center mt-n10">
                    <v-theme-provider>
                        <div class="pa-7">
                            <span class="text-h5">
                                <v-icon>
                                    mdi-fingerprint
                                </v-icon>
                                {{ $t('sign_in') }}
                            </span>
                        </div>
                    </v-theme-provider>
                </v-sheet>

                <v-card-text>
                    <v-alert v-if="form.errors.any()" type="error" dense dismissible>
                        {{ form.errors.first() }}
                    </v-alert>
                    <v-text-field
                        v-model="form.email"
                        autofocus
                        type="email"
                        :label="$t('labels.email')"
                        prepend-icon="mdi-email" />

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
                </v-card-text>
            </v-card-title>

            <v-divider class="mx-4 my-0 pa-0" />

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
        </v-card>

        <v-dialog v-model="dialogReset" max-width="500" persistent>
            <password-reset-request @close="dialogReset = false" />
        </v-dialog>
    </v-form>
</template>

<script>
    import PasswordResetRequest from '~/components/auth/PasswordResetRequest'

    export default {
        components: {
            PasswordResetRequest,
        },

        middleware: ['guest'],

        data: vm => ({
            form: vm.$vform.make({
                email: '',
                password: '',
                remember: null,
            }),
            dialogReset: null,
        }),

        head: vm => ({
            title: vm.$t('login'),
        }),
    }
</script>
