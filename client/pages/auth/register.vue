<template>
    <v-form autocomplete="off" @submit.prevent="$auth.register(form)">
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
                                    mdi-card-account-details
                                </v-icon>
                                Register
                            </span>
                        </div>
                    </v-theme-provider>
                </v-sheet>
                <v-card-text>
                    <v-alert v-if="form.errors.any()" type="error" dense dismissible>
                        {{ form.errors.first() }}
                    </v-alert>
                    <v-text-field
                        v-model="form.name"
                        autofocus
                        class="my-4"
                        label="Name"
                        prepend-icon="mdi-face" />

                    <v-text-field
                        v-model="form.email"
                        class="my-4"
                        type="email"
                        label="Email"
                        prepend-icon="mdi-email" />

                    <v-text-field
                        v-model="form.password"
                        type="password"
                        label="Password"
                        prepend-icon="mdi-lock" />

                    <v-text-field
                        v-model="form.password_confirmation"
                        type="password"
                        label="Password Confirmation"
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
                        Submit
                    </v-btn>
                    <br>
                    <v-btn
                        text
                        rounded
                        x-small
                        color="primary"
                        :disabled="form.busy"
                        :to="{ name: 'login' }">
                        Login
                    </v-btn>
                </v-col>
            </v-card-actions>
        </v-card>
    </v-form>
</template>

<script>
    import Form from 'vform'

    export default {
        middleware: 'guest',

        data: () => ({
            form: new Form({
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            })
        }),

        head: () => ({
            title: 'Register',
        }),
    }
</script>
