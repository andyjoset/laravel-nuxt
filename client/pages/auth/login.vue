<template>
    <v-form autocomplete="off" @submit.prevent="$auth.login(form)">
        <v-card
            elevation="12"
            max-width="450"
            class="mx-auto my-12">
            <v-card-title class="my-0">
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
                                Sign In
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
                        label="Email"
                        prepend-icon="mdi-email" />

                    <v-text-field
                        v-model="form.password"
                        class="mb-4"
                        type="password"
                        label="Password"
                        prepend-icon="mdi-lock" />

                    <v-checkbox
                        v-model="form.remember"
                        class="pa-0 ma-0"
                        :true-value="true"
                        :false-value="null"
                        label="Remember Me" />
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
                        Submit
                    </v-btn>

                    <br>
                    <v-btn
                        text
                        rounded
                        x-small
                        color="primary"
                        :disabled="form.busy"
                        @click="dialogReset = true">
                        Forgot Password?
                    </v-btn>

                    <v-divider vertical color="primary" />
                    <v-btn
                        text
                        rounded
                        x-small
                        color="primary"
                        :disabled="form.busy"
                        :to="{ name: 'register' }">
                        Register Now
                    </v-btn>
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

        head: () => ({
            title: 'Login',
        }),
    }
</script>
