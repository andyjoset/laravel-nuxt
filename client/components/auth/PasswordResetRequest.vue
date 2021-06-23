<template>
    <v-card>
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
                                mdi-lock-open
                            </v-icon>
                            Request Password Reset
                        </span>
                    </div>
                </v-theme-provider>
            </v-sheet>
        </v-card-title>

        <v-card-text>
            <v-alert v-if="form.errors.any()" type="error" dense dismissible>
                {{ form.errors.first() }}
            </v-alert>
            <v-form autocomplete="off" @submit.prevent="submit">
                <v-text-field
                    v-model="form.email"
                    autofocus
                    class="my-4"
                    type="email"
                    label="Email"
                    prepend-icon="mdi-email" />
            </v-form>
        </v-card-text>

        <v-divider class="mx-4 my-0 pa-0" />

        <v-card-actions>
            <v-col cols="12" class="text-right">
                <v-btn
                    color="red darken-1"
                    :disabled="form.busy"
                    @click="close">
                    Close
                </v-btn>
                <v-btn
                    color="primary"
                    :loading="form.busy"
                    @click="submit">
                    Submit
                </v-btn>
            </v-col>
        </v-card-actions>
    </v-card>
</template>

<script>
    export default {
        data: vm => ({
            form: vm.$vform.make({
                email: '',
            })
        }),

        methods: {
            async submit () {
                try {
                    const { data: status } = await this.$auth.forgotPassword(this.form, 'forgot')

                    this.$notify(status.message)

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
