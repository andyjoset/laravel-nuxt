<template>
    <v-card class="pa-2">
        <v-form autocomplete="off" @submit.prevent="submit">
            <v-card-title class="my-0">
                <v-sheet
                    rounded
                    width="100%"
                    elevation="6"
                    max-width="100%"
                    color="primary"
                    class="text-center">
                    <v-theme-provider>
                        <div class="pa-7">
                            <span class="text-h5">
                                <v-icon>
                                    mdi-shield-account
                                </v-icon>
                                {{ $t('change_password') }}
                            </span>
                        </div>
                    </v-theme-provider>
                </v-sheet>
            </v-card-title>

            <v-card-text>
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
            </v-card-text>

            <v-divider class="mx-4 my-0 pa-0" />

            <v-card-actions class="mt-0">
                <v-spacer />
                <v-btn
                    color="error"
                    :disabled="form.busy"
                    @click="close">
                    <v-icon class="mr-1">mdi-close-circle</v-icon> {{ $t('btns.cancel') }}
                </v-btn>
                <v-btn
                    type="submit"
                    color="primary"
                    :loading="form.busy">
                    <v-icon class="mr-1">mdi-check-circle</v-icon> {{ $t('btns.save') }}
                </v-btn>
            </v-card-actions>
        </v-form>
    </v-card>
</template>

<script>
    export default {
        data: vm => ({
            form: vm.$vform.make({
                password: '',
                current_password: '',
                password_confirmation: '',
            })
        }),

        methods: {
            async submit () {
                try {
                    await this.$auth.updatePassword(this.form)

                    this.$notify(this.$t('password_updated'))

                    this.close()
                } catch (e) {
                }
            },
            close () {
                this.$emit('close')
                this.form.clear()
            },
        }
    }
</script>
