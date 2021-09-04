<template>
    <v-form autocomplete="off" @submit.prevent="reset">
        <v-card
            elevation="12"
            max-width="430"
            class="mx-auto my-12">
            <v-card-title class="my-0">
                <v-sheet
                    rounded
                    width="100%"
                    elevation="6"
                    max-width="400"
                    color="primary"
                    class="overflow-hidden text-center mt-n10">
                    <v-theme-provider>
                        <div class="pa-7">
                            <span class="text-h5">
                                <v-icon>
                                    mdi-lock-open
                                </v-icon>
                                Reset Password
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
                        class="my-4"
                        type="email"
                        label="Email"
                        prepend-icon="mdi-email" />

                    <v-text-field
                        v-model="form.password"
                        type="password"
                        label="Contraseña"
                        prepend-icon="mdi-lock" />

                    <v-text-field
                        v-model="form.password_confirmation"
                        type="password"
                        label="Contraseña"
                        prepend-icon="mdi-lock" />
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
                        Reset
                    </v-btn>
                </v-col>
            </v-card-actions>
        </v-card>
    </v-form>
</template>

<script>
    export default {
        middleware: ['guest'],

        data: vm => ({
            status: '',
            form: vm.$vform.make({
                token: '',
                email: '',
                password: '',
                password_confirmation: '',
            })
        }),

        head: () => ({
            title: 'Reset Password'
        }),

        created () {
            this.form.update({
                email: this.$route.query.email,
                token: this.$route.params.token,
            })
        },

        methods: {
            async reset () {
                try {
                    const { data: status } = await this.$auth.forgotPassword(this.form, 'reset')

                    this.form.clear()

                    this.$notify(status.message)

                    this.$router.replace({ name: 'login' })
                } catch (e) {
                }
            }
        }
    }
</script>
